<?php

namespace Storm\Drivers\Base\Relational\Traits;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\RelationalTableTrait;
use \Storm\Drivers\Base\Relational\Expressions\ForeignKeyPredicateExpression;

final class ForeignKeyMode {
    const NoAction = 0;
    const Cascade = 1;
    const SetNull = 2;
    const Restrict = 3;
}

class ForeignKey extends RelationalTableTrait {
    private $Name;
    private $ParentTable;
    private $ReferencedTable;
    private $ReferencedColumnMap;
    private $ParentReferencedColumnNameMap = array();
    private $ParentColumnIdentifierMap = array();
    private $ReferencedColumnIdentifierMap = array();
    private $ParentReferencedColumnIdentifierMap = array();
    private $ReferencedParentColumnIdentifierMap = array();
    
    private $UpdateMode;
    private $DeleteMode;
    
    public function __construct(
            $Name,
            Map $ReferencedColumnMap,
            $UpdateMode = ForeignKeyMode::NoAction,
            $DeleteMode = ForeignKeyMode::NoAction) {
        
        $this->Name = $Name;
        $this->ReferencedColumnMap = $ReferencedColumnMap;
        foreach($ReferencedColumnMap as $ParentColumn) {
            $ReferencedColumn = $ReferencedColumnMap[$ParentColumn];
            
            if($this->ParentTable === null && $this->ReferencedTable === null) {
                $this->ParentTable = $ParentColumn->GetTable();
                $this->ReferencedTable = $ReferencedColumn->GetTable();
            }
            else {
                if(!$this->ParentTable->Is($ParentColumn->GetTable())) {
                    throw new \Exception;//TODO:error message
                }
                if(!$this->ReferencedTable->Is($ReferencedColumn->GetTable())) {
                    throw new \Exception;//TODO:error message
                }                
            }
            $ParentIdentifier = $ParentColumn->GetIdentifier();
            $ReferencedIdentifier = $ReferencedColumn->GetIdentifier();
            
            $this->ParentColumnIdentifierMap[$ParentIdentifier] = $ParentColumn;
            
            $this->ReferencedColumnIdentifierMap[$ReferencedIdentifier] = $ReferencedColumn;
            
            $this->ParentReferencedColumnNameMap[$ParentColumn->GetName()] = $ReferencedColumn->GetName();
            
            $this->ParentReferencedColumnIdentifierMap[$ParentIdentifier] = $ReferencedIdentifier;
            $this->ReferencedParentColumnIdentifierMap[$ReferencedIdentifier] = $ParentIdentifier;
        }
        
        $this->UpdateMode = $UpdateMode;
        $this->DeleteMode = $DeleteMode;
    }
    
    final public function GetName() {
        return $this->Name;
    }

    /**
     * @return Relational\Table
     */
    final public function GetParentTable() {
        return $this->ParentTable;
    }

    /**
     * @return Relational\Table
     */
    final public function GetReferencedTable() {
        return $this->ReferencedTable;
    }
    
    final public function GetParentColumns() {
        return $this->ParentColumnIdentifierMap;
    }
    
    final public function GetReferencedColumns() {
        return $this->ReferencedColumnIdentifierMap;
    }
    
    /**
     * @return Map
     */
    final public function GetReferencedColumnMap() {
        return $this->ReferencedColumnMap;
    }
    
    final public function GetParentReferencedColumnNameMap() {
        return $this->ParentReferencedColumnNameMap;
    }
    
    final public function GetParentColumnIdentifierMap() {
        return $this->ParentColumnIdentifierMap;
    }
    
    final public function GetParentColumnIdentifiers() {
        return array_keys($this->ParentColumnIdentifierMap);
    }

    final public function GetReferencedColumnIdentifierMap() {
        return $this->ReferencedColumnIdentifierMap;
    }

    final public function GetReferencedColumnIdentifiers() {
        return array_keys($this->ReferencedColumnIdentifierMap);
    }

    final public function GetUpdateMode() {
        return $this->UpdateMode;
    }

    final public function GetDeleteMode() {
        return $this->DeleteMode;
    }
    
    /**
     * @return ForeignKeyPredicateExpression
     */
    final public function GetConstraintPredicate() {
        return new ForeignKeyPredicateExpression($this);
    }

    protected function IsRelationalTrait(RelationalTableTrait $OtherTrait) {
        if(!$this->ReferencedTable->Is($OtherTrait->ReferencedTable))
            return false;
        if($this->UpdateMode !== $OtherTrait->UpdateMode || 
                $this->DeleteMode !== $OtherTrait->DeleteMode)
            return false;
        
        return 
            count(array_diff_assoc($this->ParentReferencedColumnNameMap, $OtherTrait->ReferencedColumnNameMap)) === 0 &&
            count(array_diff_assoc($OtherTrait->ReferencedColumnNameMap, $this->ParentReferencedColumnNameMap)) === 0; 
    }
    
    final public function MapParentToReferencedKey(array $ParentKeyData, array &$ReferencedKeyData) {
        $ParentKeyData = array_intersect_key($ReferencedKeyData, $this->ParentColumnIdentifierMap);
        array_walk($ParentKeyData, function(&$Value, $Key) use (&$ReferencedKeyData) {
            $ReferencedKeyData[$this->ParentReferencedColumnIdentifierMap[$Key]] =& $Value;
        });
    }
    
    final public function MapReferencedToParentKey(array $ReferencedKeyData, array &$ParentKeyData) {
        $ReferencedKeyData = array_intersect_key($ReferencedKeyData, $this->ReferencedColumnIdentifierMap);
        array_walk($ReferencedKeyData, function(&$Value, $Key) use (&$ParentKeyData) {
            $ParentKeyData[$this->ReferencedParentColumnIdentifierMap[$Key]] =& $Value;
        });
    }
}

?>