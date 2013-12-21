<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Relational;

interface IPropertyColumnMapping extends IPropertyMapping {
    const IPropertyColumnMappingType = __CLASS__;
    
    /**
     * @return Relational\IColumn
     */
    public function GetColumn();
    
    /**
     * @return Object\IProperty
     */
    public function AddToRelationalRequest(Relational\Request $RelationalRequest);
}

?>