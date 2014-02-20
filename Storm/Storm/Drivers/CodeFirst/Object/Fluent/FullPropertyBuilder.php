<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;

class FullPropertyBuilder extends AccessorBuilder {
    /**
     * @var PropertyBuilderBase
     */
    private $PropertyBuilder;
    
    private $ProxyGenerator;
    public function __construct(IProxyGenerator $ProxyGenerator) {
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    /**
     * @return Properties\Property
     */
    public function BuildProperty() {
        $this->VerifyProperty();
        return $this->PropertyBuilder->BuildProperty();
    }
    
    public function GetMetadata() {
        $this->VerifyProperty();
        return $this->PropertyBuilder->GetMetadata();
    }
    
    private function VerifyProperty() {
        if($this->PropertyBuilder === null) {
            throw new \Storm\Core\Object\ObjectException('Property is not defined');
        }
    }
    
    /**
     * @return DataPropertyBuilder
     */
    private function Data() {
        $this->PropertyBuilder = new DataPropertyBuilder($this->BuildAccessor());
        return $this->PropertyBuilder;
    }
    
    /**
     * @return EntityPropertyBuilder
     */
    private function Entity($EntityType) {
        $this->PropertyBuilder = new EntityPropertyBuilder($this->BuildAccessor(), $EntityType, $this->ProxyGenerator);
        return $this->PropertyBuilder;
    }
    
    /**
     * @return CollectionPropertyBuilder
     */
    private function Collection($EntityType) {
        $this->PropertyBuilder = new CollectionPropertyBuilder($this->BuildAccessor(), $EntityType, $this->ProxyGenerator);
        return $this->PropertyBuilder;
    }
    
    /**
     * @return ArrayPropertyBuilder
     */
    private function ArrayOf($EntityType) {
        $this->PropertyBuilder = new ArrayPropertyBuilder($this->BuildAccessor(), $EntityType, $this->ProxyGenerator);
        return $this->PropertyBuilder;
    }
    
    /**
     * @return DataPropertyBuilder
     */
    public function Identity() {
        return $this->Data()->Identity();
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsString($MaxLength = null) {
        return $this->Data()->AsString($MaxLength);
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsInteger() {
        return $this->Data()->AsInteger();
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsDouble() {
        return $this->Data()->AsDouble();
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsBoolean() {
        return $this->Data()->AsBoolean();
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsBinary() {
        return $this->Data()->AsBinary();
    }
    
    /**
     * @return DataPropertyOptionsBuilder
     */
    public function AsDateTime() {
        return $this->Data()->AsDateTime();
    }
    
    /**
     * @return EntityPropertyOptionsBuilder
     */
    public function AsEntityOf($EntityType) {
        return $this->Entity($EntityType)->GetEntityOptionsBuilder();
    }
    
    /**
     * @return CollectionOptionsBuilder
     */
    public function AsCollectionOf($EntityType) {
        return $this->Collection($EntityType)->GetCollectionOptionsBuilder();
    }
    
    /**
     * @return ArrayPropertyOptionsBuilder
     */
    public function AsArrayOf($EntityType) {
        return $this->ArrayOf($EntityType)->GetArrayOptionsBuilder();
    }
}