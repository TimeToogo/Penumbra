<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;

class JoinTableRelation extends Relation implements Relational\IToManyRelation {
    private $JoinTable;
    
    public function __construct(JoinTable $JoinTable, $IsTableTwo = true) {
        parent::__construct($IsTableTwo ? $JoinTable->GetTable2() : $JoinTable->GetTable1(), 
                Relational\DependencyOrder::After, Relational\DependencyOrder::Before);
        
        $this->JoinTable = $JoinTable;
    }

    /**
     * @return JoinTable
     */
    public function GetJoinTable() {
        return $this->JoinTable;
    }

    public function Persist(Relational\Transaction $Transaction, Relational\Row $Row, 
            array $PersistedRelatedRows, array $DiscardedPrimaryKeys) {
        
        $PrimaryKey = $Row->GetPrimaryKey();
        foreach($PersistedRelatedRows as $RelatedRow) {
            $Transaction->Persist($RelatedRow);
            
            $RelatedPrimaryKey = $RelatedRow->GetPrimaryKey();
            $JoinRow = $this->JoinTable->JoinRow($PrimaryKey, $RelatedPrimaryKey);
            $Transaction->Persist($JoinRow);
        }
        foreach($DiscardedPrimaryKeys as $PrimaryKey) {
            $Request = $this->MapJoinedPrimaryKey($PrimaryKey);
            $Transaction->DiscardAll($Request);
        }
    }
    public function Discard(Relational\Transaction $Transaction, Relational\PrimaryKey $PrimaryKey) {
        $Request = $this->MapJoinedPrimaryKey($PrimaryKey);
        $Transaction->DiscardAll($Request);
    }
    
    private function MapJoinedPrimaryKey(Relational\PrimaryKey $PrimaryKey) {
        $ForeignKey = $this->JoinTable->GetForeignKey($PrimaryKey->GetTable());
        $ForeignKeyData = $this->JoinTable->Row();
        $ForeignKey->MapPrimaryKey($PrimaryKey, $ForeignKeyData);

        $Request = new Relational\Request($this->JoinTable, false);
        $Predicate = Predicate::On($this->JoinTable)
                ->Matches($ForeignKeyData);
        $Request->AddPredicate($Predicate);
        
        return $Request;
    }
}

?>