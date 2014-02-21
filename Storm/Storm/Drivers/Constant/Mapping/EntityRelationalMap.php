<?php

namespace Storm\Drivers\Constant\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;
use \Storm\Drivers\Base\Mapping\Mappings;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Mapping\Mappings\LoadingMode;

abstract class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $PropertyMappings = [];
    private $DefaultLoadingMode;
        
    protected abstract function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database);
    
    protected function OnInitialize(\Storm\Core\Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        parent::OnInitialize($DomainDatabaseMap);
        $this->DefaultLoadingMode = LoadingMode::Lazy;
    }
    
    final protected function Map(Object\IProperty $Property) {
        return new FluentPropertyMapping($Property,
                $this->DefaultLoadingMode,
                function (\Storm\Core\Mapping\IPropertyMapping $Mapping) {
                    $this->PropertyMappings[] = $Mapping;
                });
    }
    
    final protected function RegisterPropertyMappings(Registrar $Registrar, 
            Object\IEntityMap $EntityMap, Relational\Database $Database) {
        $this->PropertyMappings = [];
        $this->InitializeMappings($EntityMap, $Database);
        $Registrar->RegisterAll($this->PropertyMappings);
    }
}

final class FluentPropertyMapping {
    private $Property;
    private $DefaultLoadingMode;
    private $PropertyMappingCallback;
    
    public function __construct(
            Object\IProperty $Property,
            $DefaultLoadingMode,
            callable $PropertyColumnMappingCallback) {
        $this->Property = $Property;
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
        $Callback(new Mappings\DataPropertyColumnMapping($this->Property, $Column));
    }
    
    public function ToEntity(Relational\IToOneRelation $ToOneRelation, $LoadingMode = null) {
        $Callback = $this->PropertyMappingCallback;
        $Callback($this->MakeToEntityMapping($ToOneRelation, $this->GetLoadingMode($LoadingMode)));
    }
    private function MakeToEntityMapping(Relational\IToOneRelation  $ToOneRelation, $LoadingMode) {
        return new Mappings\CompositeEntityPropertyToOneRelationMapping($this->Property, $ToOneRelation, $LoadingMode);
    }
    
    public function ToCollection(Relational\IToManyRelation $ToManyRelation, $LoadingMode = null) {
        $Callback = $this->PropertyMappingCallback;
        $Callback($this->MakeToCollectionMapping($ToManyRelation, $this->GetLoadingMode($LoadingMode)));
    }
    private function MakeToCollectionMapping(Relational\IToManyRelation $ToManyRelation, $LoadingMode) {
        return new Mappings\CompositeCollectionPropertyToManyRelationMapping($this->Property, $ToManyRelation, $LoadingMode);
    }
}

?>
