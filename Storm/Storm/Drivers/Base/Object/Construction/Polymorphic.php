<?php

namespace Storm\Drivers\Base\Object\Construction;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Properties\PolymorphProperty;

class Polymorphic extends Constructor {
    /**
     * @var PolymorphProperty
     */
    private $Property;
    
    private $ValueTypeMap;
    
    /**
     * @var IConstructor
     */
    private $Constructor;
    
    public function __construct(
            PolymorphProperty $Property,
            IConstructor $Constructor) {
        $this->Property = $Property;
        $this->ValueTypeMap = $Property->HasTypeValueMap() ? array_flip($Property->GetTypeValueMap()) : null;
        $this->Constructor = $Constructor;
    }

    protected function OnSetEntityType($EntityType) {
        $this->Constructor->SetEntityType($EntityType);
    }

    public function Construct(Object\RevivalData $RevivalData) {
        $Value = $RevivalData[$this->Property];
        if($this->ValueTypeMap === null) {
            $this->Constructor->SetEntityType($Value);
        }
        else if(isset($this->ValueTypeMap[$Value])) {
            $this->Constructor->SetEntityType($this->ValueTypeMap[$Value]);
        }
        else {
            throw new Object\ObjectException(
                    'Invalid value for polymorphic constructor for entity %s: %s given, expecting %s',
                    $this->EntityType,
                    $Value,
                    implode(', ', array_keys($this->ValueTypeMap)));
        }
        
        return $this->Constructor->Construct($RevivalData);
    }

}

?>
