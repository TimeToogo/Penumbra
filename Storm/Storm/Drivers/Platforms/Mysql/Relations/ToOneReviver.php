<?php

namespace Storm\Drivers\Platforms\Mysql\Relations;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\IQueryExecutor;
use \Storm\Drivers\Base\Relational\Relations;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;

final class ToOneReviver extends Relations\ToOneReviver {    
    public function ReviveToOneRelation(IConnection $Connection,
            Relations\ToOneRelation $ToOneRelation, array $Rows) {
        
        return $this->ReviveToOne($Connection, $ToOneRelation->GetForeignKey(), 
                $ToOneRelation->GetTable(), $Rows, false);
    }
    
    public function ReviveInversedToOneRelation(IConnection $Connection,
            Relations\InversedToOneRelation $InversedToOneRelation, array $Rows) {
        
        return $this->ReviveToOne($Connection, $InversedToOneRelation->GetForeignKey(), 
                $InversedToOneRelation->GetTable(), $Rows, true);
    }
    
    private function ReviveToOne(IConnection $Connection, 
            ForeignKey $ForeignKeyConstraint, 
            Relational\Table $RelatedTable, array $Rows, $Inversed) {
        
        $ForeignKeys = array();
        
        $RelationRequest = $RelatedTable->Request();
        $Predicate = $RelationRequest->Predicate();
        
        $RuleGroup = Relational\Constraints\RuleGroup::Any();
        foreach($Rows as $Key => $Row) {
            $ForeignKeys[$Key] = $RelatedTable->Row();
            
            if($Inversed)
                $ForeignKeyConstraint->MapForeignKey($Row, $ForeignKeys[$Key]);
            else
                $ForeignKeyConstraint->MapPrimaryKey($Row, $ForeignKeys[$Key]);
            
            $RuleGroup->AddRuleGroup(Constraints\RuleGroup::Matches($ForeignKeys[$Key]));
        }
        $Predicate->AddRules($RuleGroup);
        $RelationRequest->AddPredicate($Predicate);
        
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->AppendTableColumns('SELECT #,', $RelatedTable, ',');
        $QueryBuilder->AppendIdentifier('FROM # ', [$RelatedTable->GetName()]);
        $RelatedRows = $QueryBuilder->AppendRequest($Connection, $RelationRequest);
        
        return $this->OrderRelatedRows($ForeignKeys, $RelatedRows);
    }
    
    private function OrderRelatedRows(array $ForeignKeys, array $RelatedRows) {
        $RelatedRowOrdered = array();
        
        foreach($RelatedRows as $RelatedRow) {
            $RelatedPrimaryKey = $RelatedRow->GetPrimaryKey();
            $MatchedForeignKeys = array_filter($ForeignKeys, function ($ForeignKey) use (&$RelatedPrimaryKey) { 
                return $ForeignKey->Matches($RelatedPrimaryKey);
            });
            foreach($MatchedForeignKeys as $MatchedKey => $MatchedForeignKey) {
                $RelatedRowOrdered[$MatchedKey] = $RelatedRow;
                unset($ForeignKeys[$MatchedKey]);
            }
        }
        
        return $RelatedRowOrdered;
    }
}

?>