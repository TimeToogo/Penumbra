<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Construction;

class ConstructorDefinition {
    private $Constructor;
    public function __construct(&$Constructor) {
        $this->Constructor =& $Constructor;
    }

    
    /**
     * Creates an instance of the entity without calling the constructor.
     * 
     * @return void
     */
    public function InstanceWithoutConstructor() {
        $this->Constructor = new Construction\BlankInstance();
    }
    
    /**
     * Creates an instance of the entity by cloning the supplied entity.
     * 
     * @param object $Entity The entity to be cloned
     * @return void
     */
    public function CloneOf($Entity) {
        $this->Constructor = new Construction\ClonedInstance($Entity);
    }
    
    /**
     * Creates an instance of the entity by deep cloning (serialize/unserializing) the supplied entity.
     * 
     * @param object $Entity The entity to be cloned
     * @return void
     */
    public function DeepCloneOf($Entity) {
        $this->Constructor = new Construction\DeepClonedInstance($Entity);
    }
    
    /**
     * Creates an instance of the entity by call the constructor with no parameters
     * 
     * @return void
     */
    public function WithoutParameters() {
        $this->Constructor = new Construction\EmptyConstructor();
    }
    
    /**
     * Creates an instance of the entity by call the constructor with no parameters
     * 
     * @param mixed ... The constructor paramters
     * @return void
     */
    public function WithParameters($_) {
        $this->Constructor = new Construction\ParameteratizedConstructor(func_get_args());
    }
    
    /**
     * Creates an instance with the supplied entity constructor.
     * 
     * @param Construction\IEntityConstructor $Constructor The entity constructor
     * @return void
     */
    public function AsCustom(Construction\IEntityConstructor $Constructor) {
        $this->Constructor = $Constructor;
    }
}