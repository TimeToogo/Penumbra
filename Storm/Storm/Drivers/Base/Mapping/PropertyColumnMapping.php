<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
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
    
    /**
     * @return Relational\IColumn
     */
    public function GetColumn() {
        return $this->Column;
    }
    
    public function RelationalRequest(Relational\Request $RelationalRequest) {
        $RelationalRequest->AddColumn($this->Column);
    }

    public function Revive(Mapping\RevivingContext $Context, Map $RowStateMap) {
        $Property = $this->GetProperty();
        foreach($RowStateMap as $Row) {
            $State = $RowStateMap[$Row];
            $PropertyValue = $this->Column->Retrieve($Row);
            $State[$Property] = $PropertyValue;
        }
    }

    public function Persist(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $PropertyValue = $Context->GetState()[$this->GetProperty()];
        $this->GetColumn()->Store($Context->GetRow(), $PropertyValue);
    }

    public function Discard(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) { }
}

?>