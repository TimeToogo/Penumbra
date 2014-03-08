<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions;
use \Storm\Drivers\Base\Relational\Columns;

class DataPropertyColumnMapping extends PropertyMapping implements Mapping\IDataPropertyColumnMapping {
    private $IsIdentityPrimaryKeyMapping = false;
    private $DataProperty;
    private $Column;
    
    public function __construct(
            Object\IDataProperty $DataProperty, 
            Relational\IColumn $Column) {
        parent::__construct($DataProperty);
        if($DataProperty->IsIdentity() && !$Column->IsPrimaryKey()) {
            throw new MappingException(
                    'Cannot map an identity property to a non primary key column: %s.%s',
                    $Column->GetTable()->GetName(),
                    $Column->GetName());
        }
        else if($Column->IsPrimaryKey() && !$DataProperty->IsIdentity()) {
            throw new MappingException(
                    'Cannot map an non identity property to a primary key column: %s.%s',
                    $Column->GetTable()->GetName(),
                    $Column->GetName());
        }
        else if($Column->IsPrimaryKey() && $DataProperty->IsIdentity()) {
            $this->IsIdentityPrimaryKeyMapping = true;
        }
        
        $this->DataProperty = $DataProperty;
        $this->Column = $Column;
    }
    
    public function IsIdentityPrimaryKeyMapping() {
        return $this->IsIdentityPrimaryKeyMapping;
    }
    
    public function GetPersistColumns() {
        return [$this->Column];
    }
    
    public function GetReviveColumns() {
        return [$this->Column];
    }

    /**
     * @return Object\IDataProperty
     */
    public function GetDataProperty() {
        return $this->DataProperty;
    }

    public function AddToCriterion(Relational\Criterion $Criterion) {
        
    }

    public function MapPropertyExpression() {
        return \Storm\Drivers\Base\Relational\Expressions\Expression::ReviveColumn($this->Column);
    }
    
    public function MapTraversalExpression(Relational\Criterion $Criterion, Expressions\TraversalExpression $TraversalExpression) {
        if($this->Column instanceof Columns\Column) {
            $DataType = $this->Column->GetDataType();
            if($DataType instanceof Columns\ObjectDataType) {
                
            }
        }
    }

    public function MapAssignment(Relational\Criterion $Criterion, Mapping\Expressions\Expression $AssignmentValueExpression) {
        return \Storm\Drivers\Base\Relational\Expressions\Expression::PersistData($this->Column, $AssignmentValueExpression);
    }

    public function MapBinary(Relational\Criterion $Criterion, Mapping\Expressions\Expression $OperandValueExpression) {
        
    }
    
    public function Revive(array $ColumnDataArray, array $PropertyDataArray) {
        foreach($ColumnDataArray as $Key => $ColumnData) {
            if(isset($ColumnData[$this->Column])) {
                $PropertyDataArray[$Key][$this->DataProperty] = $this->Column->ToPropertyValue($ColumnData[$this->Column]);
            }
        }
    }
    
    public function Persist(array $PropertyDataArray, array $ColumnDataArray) {
        foreach($PropertyDataArray as $Key => $PropertyData) {
            if(isset($PropertyData[$this->DataProperty])) {
                $ColumnDataArray[$Key][$this->Column] = $this->Column->ToPersistenceValue($PropertyData[$this->DataProperty]);
            }
        }
    }

}

?>