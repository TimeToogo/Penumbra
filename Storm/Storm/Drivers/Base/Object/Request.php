<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;

class Request implements Object\IRequest {
    private $EntityType;
    private $Properties = [];
    private $IsSingleEntity;
    
    /**
     * @var Object\ICriterion 
     */
    private $Criterion;
    
    public function __construct(
            $EntityOrType, 
            array $Properties, 
            $IsSingleEntity, 
            Object\ICriterion $Criterion = null) {
        
        if(is_object($EntityOrType)) {
            $EntityOrType = get_class($EntityOrType);
        }
        $this->EntityType = $EntityOrType;
        foreach($Properties as $Property) {
            $this->AddProperty($Property);
        }
        $this->IsSingleEntity = $IsSingleEntity;
        $this->Criterion = $Criterion ?: new Criterion($this->EntityType);
        if($this->Criterion->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException(
                    'The supplied criterion must be for %s, %s given',
                    $this->EntityType,
                    $this->Criterion->GetEntityType());
        }
    }
    
    private function AddProperty(Object\IProperty $Property) {
        $this->Properties[$Property->GetIdentifier()] = $Property;
    }
    
    final public function IsSingleEntity() {
        return $this->IsSingleEntity;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function GetProperties() {
        return $this->Properties;
    }
    
    public function GetCriterion() {
        return $this->Criterion;
    }
}

?>