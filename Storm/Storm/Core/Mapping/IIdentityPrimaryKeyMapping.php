<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Relational;

interface IIdentityPrimaryKeyMapping extends IPropertyColumnMapping {
    const IIdentityPrimaryKeyMappingType = __CLASS__;
    
    /**
     * @return Relational\IIdentityProperty
     */
    public function GetIdentityProperty();
    
    /**
     * @return Relational\IPrimaryKeyColumn
     */
    public function GetPrimaryKeyColumn();
    
    public function AddToRelationalRequest(Relational\Request $RelationalRequest);
}

?>