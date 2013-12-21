<?php

namespace Storm\Drivers\Platforms\Mysql\Relations;

use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Platforms\Mysql\Queries\PredicateCompiler;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Relations;
use \Storm\Drivers\Base\Relational\Constraints;

final class ToManyReviver extends Relations\ToManyReviver {
    
    public function ReviveJoinTableRelation(IConnection $Connection,
            Relations\JoinTableRelation $JoinTableRelation, 
            array $Rows) {
        $JoinTable = $JoinTableRelation->GetJoinTable();
        
        $OriginalTable = reset($Rows)->GetTables();
        $OriginalForeignKeyConstraint = $JoinTable->GetForeignKey($OriginalTable);
        $OriginalReferenceColumnMap = $OriginalForeignKeyConstraint->GetReferencedColumnMap();  
        
        $RelatedTable = $JoinTableRelation->GetTable();
        $RelatedForeignKeyConstraint = $JoinTable->GetForeignKey($RelatedTable);
        $RelatedReferenceColumnMap = $RelatedForeignKeyConstraint->GetReferencedColumnMap();        
        
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->AppendTableColumns('SELECT #,', $RelatedTable, ',');
        $QueryBuilder->AppendTableColumns('# ', $JoinTable, ',');
        $QueryBuilder->AppendIdentifier('FROM # ', [$RelatedTable->GetName()]);
        $QueryBuilder->AppendIdentifier('INNER JOIN # ', [$JoinTable->GetName()]);
        $QueryBuilder->Append('ON  ');
        $First = true;
        foreach($RelatedReferenceColumnMap as $JoinTableColumn) {
            $RelatedTableColumn = $RelatedReferenceColumnMap[$JoinTableColumn];
            
            if($First) $First = false;
            else
                $QueryBuilder->Append(' AND ');
            
            $QueryBuilder->AppendColumn('#', $JoinTable, $JoinTableColumn, false, false);
            $QueryBuilder->Append(' = ');
            $QueryBuilder->AppendColumn('#', $RelatedTable, $RelatedTableColumn, false, false);
        }
        
        $Predicate = Constraints\Predicate::On($JoinTable);
        $RuleGroup = Constraints\RuleGroup::Any();
        
        $JoinTableKeys = array();
        foreach($Rows as $Key => $Row) {
            $OriginalPrimaryKey = $Row->GetPrimaryKey();
            $JoinTableKey = $JoinTable->Row();
            $OriginalForeignKeyConstraint->MapForeignKey($OriginalPrimaryKey, $JoinTableKey);
            $JoinTableKeys[$Key] = $JoinTableKey;
            
            $RuleGroup->AddRuleGroup(Constraints\RuleGroup::Matches($JoinTableKey));
        }
        $Predicate->AddRules($RuleGroup);
        $this->AppendWhereClausePredicate($QueryBuilder, $Predicate);
        
        $JoinedRelatedRows = $Connection->LoadJoinedRows([$JoinTable, $RelatedTable], $QueryBuilder->Build());
        $RelatedRows = array();
        foreach($JoinTableKeys as $Key => $OtherJoinTableKey) {
            $RelatedRows[$Key] = array();
        }
        
        foreach($JoinedRelatedRows as $JoinedRelatedRow) {
            $JoinTableKey = $JoinedRelatedRow->GetRow($JoinTable);
            
            foreach($JoinTableKeys as $Key => $OtherJoinTableKey) {
                if($OtherJoinTableKey->Matches($JoinTableKey)) {
                    $RelatedRows[$Key][] = $JoinedRelatedRow->GetRow($RelatedTable);
                    break;
                }
            }
        }
        
        return $RelatedRows;
    }

    public function ReviveToManyRelation(IConnection $Connection, 
            Relations\ToManyRelation $ToManyRelation, array $Rows) {
        $RelatedTable = $ToManyRelation->GetTable();
        $ForeignKeyConstraint = $ToManyRelation->GetForeignKey();
        
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->AppendTableColumns('SELECT # ', $RelatedTable, ',');
        $QueryBuilder->AppendIdentifier('FROM # ', [$RelatedTable->GetName()]);
        
        $Predicate = Constraints\Predicate::On($RelatedTable);
        $RuleGroup = Constraints\RuleGroup::Any();
        
        $RelatedTableForeignKeys = array();
        foreach($Rows as $Key => $Row) {
            $OriginalPrimaryKey = $Row->GetPrimaryKey();
            $RelatedTableKey = $RelatedTable->Row();
            $ForeignKeyConstraint->MapForeignKey($OriginalPrimaryKey, $RelatedTableKey);
            $RelatedTableForeignKeys[$Key] = $RelatedTableKey;
            
            $RuleGroup->AddRuleGroup(Constraints\RuleGroup::Matches($RelatedTableKey));
        }
        $Predicate->AddRules($RuleGroup);
        $this->AppendWhereClausePredicate($QueryBuilder, $Predicate);        
        
        $RelatedRows = $Connection->LoadRows($RelatedTable, $QueryBuilder->Build());
        
        return $this->GroupRelatedRows($RelatedTableForeignKeys, $RelatedRows);
    }
    
    private function AppendWhereClausePredicate(QueryBuilder $QueryBuilder, Constraints\Predicate $Predicate) {
        $QueryBuilder->Append('WHERE TRUE AND ');
        $QueryBuilder->Append('(');
        $QueryBuilder->AppendPredicate($Predicate);
        $QueryBuilder->Append(')');
    }
    
    private function GroupRelatedRows(array $ForeignKeys, array $RelatedRows) {
        $RelatedRowOrdered = array();
        
        $RelatedPrimaryKeys = array();
        foreach($RelatedRows as $Key => $RelatedRow) {
            $RelatedPrimaryKeys[$Key] = $RelatedRow->GetPrimaryKey();
        }
        
        foreach($ForeignKeys as $Key => $ForeignKey) {
            $MatchedRelatedPrimaryKeys = array_filter($RelatedPrimaryKeys, function ($RelatedPrimaryKey) use (&$ForeignKey) { 
                return $ForeignKey->Matches($RelatedPrimaryKey);
            });
            
            $RelatedRowOrdered[$Key] = array();
            foreach($MatchedRelatedPrimaryKeys as $OtherKey => $MatchedRelatedPrimaryKey) {
                $RelatedRowOrdered[$Key][] = $RelatedRows[$OtherKey];
                unset($RelatedPrimaryKeys[$OtherKey]);
            }
        }
        
        return $RelatedRowOrdered;
    }

}

?>