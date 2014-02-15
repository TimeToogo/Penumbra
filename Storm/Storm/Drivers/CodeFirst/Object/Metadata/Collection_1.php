<?php

namespace Storm\Drivers\CodeFirst\Object\Metadata;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;

class Collection implements \IteratorAggregate {
    private $Metadata = array();
    
    public function __construct(array $Metadata = array()) {
        array_walk($Metadata, [$this, 'Add']);
    }
    
    public function Add(Metadata $Metadata) {
        if($Metadata->AllowMultiple()) {
            $this->Metadata[] = $Metadata;
        }
        else {
            $MetadataType = $Metadata->GetType();
            foreach($this->Metadata as $Key => $OtherMetadata) {
                if($OtherMetadata instanceof $MetadataType) {
                    $this->Metadata[$Key] = $Metadata;
                    return;
                }
            }
        }
    }
    
    public function HasType($MetadataType) {
        foreach($this->Metadata as $Metadata) {
            if($Metadata instanceof $MetadataType) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return Metadata[]
     */
    public function GetAllOfType($MetadataType) {
        $MetadataOfType = array();
        foreach($this->Metadata as $Metadata) {
            if($Metadata instanceof $MetadataType) {
                $MetadataOfType[] = $Metadata;
            }
        }
        return $MetadataOfType;
    }
    
    /**
     * @return Metadata|null
     */
    public function GetFirstOfType($MetadataType) {
        foreach($this->Metadata as $Metadata) {
            if($Metadata instanceof $MetadataType) {
                return $Metadata;
            }
        }
        return null;
    }

    public function getIterator() {
        return new \ArrayIterator($this->Metadata);
    }
}

?>