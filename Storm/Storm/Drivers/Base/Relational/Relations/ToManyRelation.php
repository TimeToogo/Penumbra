<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToManyRelation extends Relation implements Relational\IToManyRelation {    
    private $ForeignKey;
    
    public function __construct(Relational\Table $RelatedTable, ForeignKey $ForeignKey) {
        parent::__construct($RelatedTable, 
                Relational\DependencyOrder::Before, Relational\DependencyOrder::Before);
        
        $this->ForeignKey = $ForeignKey;
    }
    
    /**
     * @return ForeignKey
     */
    public function GetForeignKey() {
        return $this->ForeignKey;
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\Row $Row, array $PersistedRelatedRows, array $DiscardedPrimaryKeys) {
        foreach($PersistedRelatedRows as $PersistedRelatedRow) {
            $Transaction->Persist($PersistedRelatedRow);
        }
        foreach($DiscardedPrimaryKeys as $DiscardedPrimaryKey) {
            $Transaction->Discard($DiscardedPrimaryKey);
        }
    }
    
    public function Discard(Relational\Transaction $Transaction, Relational\PrimaryKey $PrimaryKey) {
        $RelatedPrimaryKey = $this->GetTable()->Row();
        $this->ForeignKey->MapPrimaryKey($PrimaryKey, $RelatedPrimaryKey);
        
        $Table = $RelatedPrimaryKey->GetTable();
        
        $Request = new Relational\Request($Table);
        $Request->AddPredicate(Predicate::On($Table)
                        ->Matches($RelatedPrimaryKey));
        
        $Transaction->DiscardAll($Request);
    }
}

?>