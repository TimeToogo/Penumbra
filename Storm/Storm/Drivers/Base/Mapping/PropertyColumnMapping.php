<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Mapping\IPropertyColumnMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class PropertyColumnMapping extends PropertyMapping implements IPropertyColumnMapping {
    private $Column;
    
    public function __construct(
            Object\IProperty $Property, 
            Relational\IColumn $Column) {
        parent::__construct($Property);
        
        $this->Column = $Column;
    }
    
    public function GetPersistColumns() {
        return [$this->Column];
    }
    
    public function GetReviveColumns() {
        return [$this->Column];
    }
    
    public function AddToRelationalRequest(Relational\Request $RelationalRequest) {
        $RelationalRequest->AddColumns($this->GetReviveColumns());
    }

    public function Revive(Map $ResultRowRevivalDataMap) {
        $Property = $this->GetProperty();
        foreach($ResultRowRevivalDataMap as $Row) {
            $RevivalData = $ResultRowRevivalDataMap[$Row];
            $RevivalValue = $this->Column->Retrieve($Row);
            $RevivalData[$Property] = $RevivalValue;
        }
    }

    public function Persist(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        
        $PropertyValue = $Context->GetState()[$this->GetProperty()];
        $this->Column->Store($Context->GetColumnData(), $PropertyValue);
    }

    public function Discard(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) { }
}

?>