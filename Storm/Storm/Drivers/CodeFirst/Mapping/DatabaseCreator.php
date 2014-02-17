<?php

namespace Storm\Drivers\CodeFirst\Mapping;

use \Storm\Drivers\CodeFirst\Object\EntityMap;
use \Storm\Drivers\Dynamic\Relational\Table;
use \Storm\Core\Object\IDataProperty;
use \Storm\Core\Object\IEntityProperty;
use \Storm\Core\Object\ICollectionProperty;
use \Storm\Core\Mapping\IPropertyMapping;
use \Storm\Drivers\Base\Mapping\Mappings;
use \Storm\Drivers\CodeFirst\Object\Metadata;
use \Storm\Drivers\Base\Relational\IPlatform;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\Relations;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Storm\Drivers\Base\Relational\Columns\Column;

class DatabaseCreator {
    /**
     * @var IPropertyMappings[] 
     */
    private $GroupedMappings = array();
    /**
     * @var EntityMap[] 
     */
    private $EntityMaps;
    /**
     * @var Tables[] 
     */
    private $Tables;
    public function __construct(array $EntityMaps) {
        $this->EntityMaps = $EntityMaps;
    }
    
    public function GetGroupedMappings() {
        return $this->GroupedMappings;
    }
    
    /**
     * @return Table[]
     */
    public function GenerateTables(IPlatform $Platform) {
        $this->Tables = array();
        foreach($this->EntityMaps as $EntityMap) {
            $this->Tables[] = $this->GenerateTable($EntityMap, $Platform);
        }
        
        return $this->Tables;
    }
    
    private function GenerateTable(EntityMap $EntityMap, IColumnSet $ColumnSet) {
        $Columns = array();
        $StructuralTraits = array();
        $RelationalTraits = array();
        $ToOneRelations = array();
        $ToManyRelations = array();
        
        foreach($EntityMap->GetDataProperties() as $Property) {
            $Column = $this->GenerateColumn($Property, $EntityMap->GetMetadata($Property), $ColumnSet);
            $this->AddMapping($EntityMap, new Mappings\DataPropertyColumnMapping($Property, $Column));
            $Columns[] = $Column;
        }
        foreach($EntityMap->GetEntityProperties() as $Property) {
            $ToOneRelation = $this->GenerateToOneRelation($Columns, $Property, $EntityMap->GetMetadata($Property), $ColumnSet);
            $this->AddMapping($EntityMap, new Mappings\DataPropertyColumnMapping($Property, $Column));
            $ToOneRelations[] = $Column;
        }
        foreach($EntityMap->GetCollectionProperties() as $Property) {
            $Columns[] = $this->GenerateToManyRelation($Columns, $Property, $EntityMap->GetMetadata($Property), $ColumnSet);
        }
        
        return new Table($Name, $KeyGenerator, $Columns, $StructuralTraits, $RelationalTraits, $ToOneRelations, $ToManyRelations);
    }
    
    private function AddMapping(EntityMap $EntityMap, IPropertyMapping $Mapping) {
        $EntityType = $EntityMap->GetEntityType();
        if(!isset($this->GroupedMappings[$EntityType])) {
            $this->GroupedMappings[$EntityType] = array();
        }
        
        $this->GroupedMappings[$EntityType][] = $Mapping;
    }
    
    private function GenerateColumn(IDataProperty $Property, Metadata\Collection $Metadata, IColumnSet $ColumnSet) {
        $DataTypeMetadata = $Metadata->GetFirstOfType(Metadata\DataType::GetType());
        if($DataTypeMetadata === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    'Could not map data property of entity %s to column as no data type metadata was supplied',
                    $Property->GetEntityMap()->GetEntityType());
        }
        $NameMetadata = $Metadata->GetFirstOfType(Metadata\Name::GetType());
        if($NameMetadata === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    'Could not map data property of entity %s to column as no name metadata was supplied',
                    $Property->GetEntityMap()->GetEntityType());
        }
        $DataType = $DataTypeMetadata->GetDataType();
        $Name = $NameMetadata->GetName();
        
        if($Property->IsIdentity()) {
            return $this->GeneratePrimaryKey($Property, $Metadata, $Name, $DataType, $ColumnSet);
        }
        else {
            return $this->GenerateDataColumn($Property, $Metadata, $Name, $DataType, $ColumnSet);
        }
    }
    
    private function GenerateDataColumn(IDataProperty $Property, Metadata\Collection $Metadata, $Name, $DataType, IColumnSet $ColumnSet) {
        switch ($DataType) {
            case Metadata\DataType::String:
                $Length = $Metadata->HasType(Metadata\MaxLength::GetType()) ? 
                        $Metadata->GetFirstOfType(Metadata\MaxLength::GetType())->GetMaxLength() : 4000;
                return $ColumnSet->String($Name, $Length);
                
            case Metadata\DataType::Integer:
                return $ColumnSet->Int32($Name);
                
            case Metadata\DataType::Binary:
                return $ColumnSet->Boolean($Name);
                
            case Metadata\DataType::Double:
                return $ColumnSet->Double($Name);
                
            case Metadata\DataType::DateTime:
                return $ColumnSet->DateTime($Name);
                
            case Metadata\DataType::Binary:
                $Length = $Metadata->HasType(Metadata\MaxLength::GetType()) ? 
                        $Metadata->GetFirstOfType(Metadata\MaxLength::GetType())->GetMaxLength() : 4000;
                return $ColumnSet->Binary($Name, $Length);

            default:
                throw new \Storm\Core\Mapping\MappingException(
                        'Could not map data property of entity %s to column as the data type metadata had an invalid value: %s',
                        $Property->GetEntityMap()->GetEntityType(),
                        $DataType);
        }
    }
    
    private function GeneratePrimaryKey(IDataProperty $Property, Metadata\Collection $Metadata, $Name, $DataType, IColumnSet $ColumnSet) {
        switch ($DataType) {
            case Metadata\DataType::String:
                return $ColumnSet->Guid($Name);

            case Metadata\DataType::Integer:
                return $ColumnSet->IncrementInt32($Name);

            default:
                throw new \Storm\Core\Mapping\MappingException(
                        'Could not map identity property of entity %s to column as the data type metadata had an unsupported value: %s',
                        $Property->GetEntityMap()->GetEntityType(),
                        $DataType);
        }
    }
    
    private function GenerateToOneRelation(array &$Columns, IEntityProperty $Property, Metadata\Collection $Metadata, IColumnSet $ColumnSet) {
        
    }
    
    private function GenerateToManyRelation(array &$Columns, ICollectionProperty $Property, Metadata\Collection $Metadata, IColumnSet $ColumnSet) {
        
    }
}

?>