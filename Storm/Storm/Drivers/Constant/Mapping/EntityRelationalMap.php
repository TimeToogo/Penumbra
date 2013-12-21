<?php

namespace Storm\Drivers\Constant\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class LoadingMode {
    const Eager = 0;
    const Lazy = 1;
    const ExtraLazy = 2;
}

abstract class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $PropertyMappings = array();
    private $ProxyGenerator;
    private $DefaultLoadingMode;
    
    public function __construct() {
        parent::__construct();
    }
    
    protected abstract function InitializeMappings(Object\EntityMap $EntityMap, Relational\Table $Table);
    
    protected function OnInitialize(\Storm\Core\Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        parent::OnInitialize($DomainDatabaseMap);
        $this->ProxyGenerator = $this->GetProxyGenerator();
        $this->DefaultLoadingMode = $this->GetMappingConfiguration()->GetDefaultLoadingMode();
    }
    
    final protected function Map(Object\IProperty $Property) {
        return new FluentPropertyMapping($Property,
                $this->ProxyGenerator,
                $this->DefaultLoadingMode,
                function (\Storm\Core\Mapping\IPropertyMapping $Mapping) {
                    $this->PropertyMappings[] = $Mapping;
                });
    }
    
    final protected function RegisterPropertyMappings(Registrar $Registrar, 
            Object\EntityMap $EntityMap, Relational\Table $Table) {
        $this->PropertyMappings = array();
        $this->InitializeMappings($EntityMap, $Table);
        $Registrar->RegisterAll($this->PropertyMappings);
    }
}

final class FluentPropertyMapping {
    private $Property;
    private $ProxyGenerator;
    private $DefaultLoadingMode;
    private $PropertyMappingCallback;
    
    public function __construct(
            Object\IProperty $Property,
            Mapping\Proxy\ProxyGenerator $ProxyGenerator,
            $DefaultLoadingMode,
            callable $PropertyColumnMappingCallback) {
        $this->Property = $Property;
        $this->ProxyGenerator = $ProxyGenerator;
        $this->DefaultLoadingMode = $DefaultLoadingMode;
        $this->PropertyMappingCallback = $PropertyColumnMappingCallback;
    }
    
    private function GetLoadingMode($SuppliedLoadingMode) {
        if($SuppliedLoadingMode !== LoadingMode::Eager
                && $SuppliedLoadingMode !== LoadingMode::Lazy
                && $SuppliedLoadingMode !== LoadingMode::ExtraLazy)
            return $this->DefaultLoadingMode;
        else
            return $SuppliedLoadingMode;
    }
    
    public function ToColumn(Relational\IColumn $Column) {
        $Callback = $this->PropertyMappingCallback;
        $Callback(new Mapping\PropertyColumnMapping($this->Property, $Column));
    }
    
    public function ToEntity(Relational\IToOneRelation $ToOneRelation, $LoadingMode = null) {
        $Callback = $this->PropertyMappingCallback;
        $Callback($this->MakeToEntityMapping($ToOneRelation, $this->GetLoadingMode($LoadingMode)));
    }
    private function MakeToEntityMapping(Relational\IToOneRelation  $ToOneRelation, $LoadingMode) {
        switch ($LoadingMode) {
            case LoadingMode::Eager:
                return new Mapping\EagerPropertyEntityMapping($this->Property, $ToOneRelation);
            case LoadingMode::Lazy:
                return new Mapping\LazyPropertyEntityMapping($this->Property, $ToOneRelation, $this->ProxyGenerator);
            case LoadingMode::Eager:
                return new Mapping\ExtraLazyPropertyEntityMapping($this->Property, $ToOneRelation, $this->ProxyGenerator);
            default:
                throw new \InvalidArgumentException('Unsupported loading mode');
        }
    }
    
    public function ToCollection(Relational\IToManyRelation $ToManyRelation, $LoadingMode = null) {
        $Callback = $this->PropertyMappingCallback;
        $Callback($this->MakeToCollectionMapping($ToManyRelation, $this->GetLoadingMode($LoadingMode)));
    }
    private function MakeToCollectionMapping(Relational\IToManyRelation $ToManyRelation, $LoadingMode) {
        switch ($LoadingMode) {
            case LoadingMode::Eager:
                return new Mapping\EagerPropertyCollectionMapping($this->Property, $ToManyRelation);
            case LoadingMode::Lazy:
                return new Mapping\LazyPropertyCollectionMapping($this->Property, $ToManyRelation, $this->ProxyGenerator);
            case LoadingMode::Eager:
                return new Mapping\ExtraLazyPropertyCollectionMapping($this->Property, $ToManyRelation, $this->ProxyGenerator);
            default:
                throw new \InvalidArgumentException('Unsupported loading mode');
        }
    }
}

?>
