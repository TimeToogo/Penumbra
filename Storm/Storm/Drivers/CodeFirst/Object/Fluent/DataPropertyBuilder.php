<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class DataPropertyBuilder extends PropertyBuilderBase {
    private $IsIdentity = false;
    
    public function __construct(Properties\Accessors\Accessor $Accessor) {
        parent::__construct($Accessor, new DataPropertyOptionsBuilder());
        
        if($Accessor instanceof Properties\Accessors\Field) {
            $this->Metadata->Add(new Metadata\Name($Accessor->GetFieldName()));
        }
        else if($Accessor instanceof Properties\Accessors\MethodPair) {
            $GetterName = $Accessor->GetPropertyGetter()->GetMethodName();
            if(stripos('get', $GetterName) === 0) {
                $GetterName = substr($GetterName, 3);
            }
            $this->Metadata->Add(new Metadata\Name($GetterName));
        }
        else if($Accessor instanceof Properties\Accessors\Indexer
                && is_string($Accessor->GetIndex())) {
            $this->Metadata->Add(new Metadata\Name($Accessor->GetIndex()));
        }
    }
    
    public function BuildProperty() {
        return new Properties\DataProperty($this->Accessor, $this->IsIdentity);
    }
    
    public function Identity() {
        return $this->Identity();
    }
    
    /**
     * @param int $MaxLength
     * @return DataPropertyOptionsBuilder
     */
    public function AsString($MaxLength = null) {
        $this->Metadata->Add(new Metadata\DataType(Metadata\DataType::String));
        if($MaxLength !== null) {
            $this->Metadata->Add(new Metadata\MaxLength($MaxLength));
        }
        
        return $this->PropertyOptions;
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsInteger() {  
        $this->Metadata->Add(new Metadata\DataType(Metadata\DataType::Integer)); 
        
        return $this->PropertyOptions;
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsDouble() {  
        $this->Metadata->Add(new Metadata\DataType(Metadata\DataType::Double)); 
        
        return $this->PropertyOptions;
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsBoolean() {  
        $this->Metadata->Add(new Metadata\DataType(Metadata\DataType::Boolean)); 
        
        return $this->PropertyOptions;
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsBinary() {  
        $this->Metadata->Add(new Metadata\DataType(Metadata\DataType::Binary)); 
        
        return $this->PropertyOptions;
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsDateTime() {  
        $this->Metadata->Add(new Metadata\DataType(Metadata\DataType::DateTime)); 
        
        return $this->PropertyOptions;
    }
}