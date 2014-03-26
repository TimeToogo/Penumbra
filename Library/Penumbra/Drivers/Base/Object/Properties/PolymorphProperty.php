<?php

namespace Penumbra\Drivers\Base\Object\Properties;

use \Penumbra\Core\Object;
use \Penumbra\Core\Object\IDataProperty;
use \Penumbra\Core\Object\Expressions as O;

class PolymorphProperty extends PropertyBase implements IDataProperty {
    private $TypeValueMap;    
    private $IsIdentity;
    
    public function __construct(array $TypeValueMap = null, $IsIdentity = false) {
        parent::__construct('get_class($Entity)');
        $this->TypeValueMap = $TypeValueMap;
        $this->IsIdentity = $IsIdentity;
    }
    
    protected function OnSetEntityType($EntityType) {}
    
    public function GetTypeValueMap() {
        return $this->TypeValueMap;
    }
    
    public function HasTypeValueMap() {
        return $this->TypeValueMap !== null;
    }

    public function IsIdentity() {
        return $this->IsIdentity;
    }
    
    public function GetValue($Entity) {
        $Type = get_class($Entity);
        
        if($this->TypeValueMap !== null) {
            if(!isset($this->TypeValueMap[$Type])) {
                throw new Object\ObjectException(
                        'Invalid type for polymorphic entity: %s, expecting one of %s',
                        $Type,
                        implode(', ', array_keys($this->TypeValueMap)));
            }
            
            return $this->TypeValueMap[$Type];
        }
        
        return $Type;
    }

    public function ReviveValue($PropertyValue, $Entity) {}
    public function ResolveTraversalExpression(Object\Expressions\TraversalExpression $Expression) { }
}

?>