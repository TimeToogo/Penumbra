<?php

namespace Penumbra\Drivers\Constant\Mapping;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Drivers\Base\Mapping;
use \Penumbra\Drivers\Base\Mapping\Mappings;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Mapping\Mappings\Loading;

abstract class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $PropertyMappings = [];
    private $DefaultLoadingMode;
        
    protected abstract function InitializeMappings(Object\IEntityMap $EntityMap, Relational\Database $Database);
    
    protected function OnInitialize(\Penumbra\Core\Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        parent::OnInitialize($DomainDatabaseMap);
        $this->DefaultLoadingMode = Loading\Mode::RequestScopeLazy;
    }
    
    final protected function Map(Object\IProperty $Property) {
        return new FluentPropertyMapping($Property,
                $this->DefaultLoadingMode,
                function (\Penumbra\Core\Mapping\IPropertyMapping $Mapping) {
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
        if($SuppliedLoadingMode !== Loading\Mode::Eager
                && $SuppliedLoadingMode !== Loading\Mode::GlobalScopeLazy
                && $SuppliedLoadingMode !== Loading\Mode::RequestScopeLazy
                && $SuppliedLoadingMode !== Loading\Mode::ParentScopeLazy)
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
        return new Mappings\EntityPropertyToOneRelationMapping($this->Property, $ToOneRelation, $this->GetEntityLoading($LoadingMode));
    }
    
    public function ToCollection(Relational\IToManyRelation $ToManyRelation, $LoadingMode = null) {
        $Callback = $this->PropertyMappingCallback;
        $Callback($this->MakeToCollectionMapping($ToManyRelation, $this->GetLoadingMode($LoadingMode)));
    }
    private function MakeToCollectionMapping(Relational\IToManyRelation $ToManyRelation, $LoadingMode) {
        return new Mappings\CollectionPropertyToManyRelationMapping($this->Property, $ToManyRelation, $this->GetCollectionLoading($LoadingMode));
    }
    
    private static $EntityLoadingModes;
    private function GetEntityLoading($LoadingMode) {
        if(self::$EntityLoadingModes === null) {
            self::$EntityLoadingModes = [
                Mappings\Loading\Mode::Eager => new Mappings\Loading\EagerEntityLoading(),
                Mappings\Loading\Mode::GlobalScopeLazy => new Mappings\Loading\GlobalScopeLazyEntityLoading(),
                Mappings\Loading\Mode::RequestScopeLazy => new Mappings\Loading\RequestScopeEntityLoading(),
                Mappings\Loading\Mode::ParentScopeLazy => new Mappings\Loading\ParentScopeLazyEntityLoading(),
            ];
        }
        
        return self::$EntityLoadingModes[$LoadingMode];
    }
    
    private static $CollectionLoadingModes;
    private function GetCollectionLoading($LoadingMode) {
        if(self::$CollectionLoadingModes === null) {
            self::$CollectionLoadingModes = [
                Mappings\Loading\Mode::Eager => new Mappings\Loading\EagerCollectionLoading(),
                Mappings\Loading\Mode::GlobalScopeLazy => new Mappings\Loading\GlobalScopeCollectionLoading(),
                Mappings\Loading\Mode::RequestScopeLazy => new Mappings\Loading\RequestScopeCollectionLoading(),
                Mappings\Loading\Mode::ParentScopeLazy => new Mappings\Loading\ParentScopeCollectionLoading(),
            ];
        }
        
        return self::$CollectionLoadingModes[$LoadingMode];
    }
}

?>
