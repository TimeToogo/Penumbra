<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

abstract class PropertyBuilderBase {
    /**
     * @var Metadata\Collection 
     */
    protected $Metadata = array();
    
    /**
     * @var Properties\Accessors\Accessor 
     */
    protected $Accessor;
    
    /**
     * @var PropertyOptionsBuilder 
     */
    protected $PropertyOptions;
    
    public function __construct(Properties\Accessors\Accessor $Accessor, PropertyOptionsBuilder $PropertyOptionsBuilder) {
        $this->Accessor = $Accessor;
        $this->Metadata = new Metadata\Collection();
        $PropertyOptionsBuilder->SetMetadata($this->Metadata);
        $this->PropertyOptions = $PropertyOptionsBuilder;
    }
    
    /**
     * @return Metadata\Collection 
     */
    final public function GetMetadata() {
        return $this->Metadata;
    }
    
    public abstract function BuildProperty();
}