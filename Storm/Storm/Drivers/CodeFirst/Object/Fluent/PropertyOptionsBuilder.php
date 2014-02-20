<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

abstract class PropertyOptionsBuilder {
    /**
     * @var Metadata\Collection
     */
    protected $Metadata;
    
    final public function SetMetadata(Metadata\Collection $Metadata) {
        return $this->Metadata = $Metadata;
    }
    
    final public function GetMetadata() {
        return $this->Metadata;
    }
    
    public function With(Metadata\Metadata $Metadata) {
        $this->Metadata->Add($Metadata);
    }    
}