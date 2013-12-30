<?php

namespace Storm\Drivers\Constant\Mapping;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Mapping;
use \Storm\Drivers\Base\Mapping\Mappings;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class LoadingMode {
    const Eager = 0;
    const Lazy = 1;
    const ExtraLazy = 2;
}

abstract class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $PropertyMappings = array();
    private $DefaultLoadingMode;
        
    protected abstract function InitializeMappings(Object\EntityMap $EntityMap, Relational\Database $Database);
    
    protected function OnInitialize(\Storm\Core\Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        parent::OnInitialize($DomainDatabaseMap);
        $this->DefaultLoadingMode = $this->GetMappingConfiguration()->GetDefaultLoadingMode();
    }
    
    final protected function Map(Object\IProperty $Property) {
        return new FluentPropertyMapping($Property,
                $this->DefaultLoadingMode,
                function (\Storm\Core\Mapping\IPropertyMapping $Mapping) {
                    $this->PropertyMappings[] = $Mapping;
                });
    }
    
    final protected function RegisterPropertyMappings(Registrar $Registrar, 
            Object\EntityMap $EntityMap, Relational\Database $Database) {
        $this->PropertyMappings = array();
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
        switch ($LoadingMode) {
            case LoadingMode::Eager:
                return new Mappings\EagerEntityPropertyToOneRelationMapping($this->Property, $ToOneRelation);
            case LoadingMode::Lazy:
                return new Mappings\LazyEntityPropertyToOneRelationMapping($this->Property, $ToOneRelation);
            case LoadingMode::ExtraLazy:
                return new Mappings\ExtraLazyEntityPropertyToOneRelationMapping($this->Property, $ToOneRelation);
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
                return new Mappings\EagerCollectionPropertyToManyRelationMapping($this->Property, $ToManyRelation);
            case LoadingMode::Lazy:
                return new Mappings\LazyCollectionPropertyToManyRelationMapping($this->Property, $ToManyRelation);
            case LoadingMode::ExtraLazy:
                return new Mappings\ExtraLazyCollectionPropertyToManyRelationMapping($this->Property, $ToManyRelation);
            default:
                throw new \InvalidArgumentException('Unsupported loading mode');
        }
    }
}

?>
