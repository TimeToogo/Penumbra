<?php

namespace Penumbra\Drivers\Platforms\Development\Syncing;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Relational\Syncing\IDatabaseBuilder;
use \Penumbra\Drivers\Base\Relational\Syncing\IDatabaseModifier;

final class DatabaseSyncer extends Relational\Syncing\DatabaseSyncer {
    private $IdentifiersAreCaseSensitive;
    private $DropUnspecifiedTables;
    private $DropUnspecifiedColumns;
    
    public function __construct(
            IDatabaseBuilder $Builder, IDatabaseModifier $Modifier,
            $IdentifiersAreCaseSensitive = true, 
            $DropUnspecifiedTables = false, 
            $DropUnspecifiedColumns = false) {
        parent::__construct($Builder, $Modifier);
        
        $this->IdentifiersAreCaseSensitive = $IdentifiersAreCaseSensitive;
        $this->DropUnspecifiedTables = $DropUnspecifiedTables;
        $this->DropUnspecifiedColumns = $DropUnspecifiedColumns;
    }
    
    final public function SetIdentifiersAreCaseSensitive($IdentifiersAreCaseSensitive) {
        $this->IdentifiersAreCaseSensitive = $IdentifiersAreCaseSensitive;
    }
    
    protected function SyncDatabase(
            IConnection $Connection, Relational\Database $Database, 
            IDatabaseBuilder $Builder, IDatabaseModifier $Modifier) {
        
         $CurrentDatabase = $Builder->Build($Connection);
         
         $this->DropRelationalTraits($Connection, $Modifier, $CurrentDatabase);
         $Tables = $Database->GetTables();
         $CurrentTables = $CurrentDatabase->GetTables();
         
         if(!$this->IdentifiersAreCaseSensitive) {
             $Tables = array_change_key_case($Tables);
             $CurrentTables = array_change_key_case($CurrentTables);
         }
         
         foreach($Tables as $TableName => $Table) {
             $this->SyncTable($Connection, $Modifier, $Table, isset($CurrentTables[$TableName]) ? $CurrentTables[$TableName] : null);
         }
         foreach($CurrentDatabase->GetTables() as $TableName => $CurrentTable) {
             if(!isset($CurrentTables[$TableName])) {
                 $this->SyncTable($Connection, $Modifier, null, $CurrentTable);
             }
         }
         $this->AddRelationalTraits($Connection, $Modifier, $Database);
    }
    
    private function SyncTable(IConnection $Connection, IDatabaseModifier $Modifier, 
            Relational\Table $Table = null, Relational\Table $CurrentTable = null) {
        
        if($Table === null && $CurrentTable === null)
            return;
        
        if($CurrentTable === null) {
            $this->CreateTable($Connection, $Modifier, $Table);
        }
        else if ($Table === null) {
            if($this->DropUnspecifiedTables) {
                $Modifier->DropTable($Connection, $CurrentTable);
            }
        }
        else {
            list($TraitsToAdd, $TraitsToRemove) = 
                    $this->GetTableStructuralTraits($Table, $CurrentTable);
            
            list($ColumnsToModify, $ColumnsToAdd, $ColumnsToRemove) = 
                    $this->GetTableColumns($Table->GetColumns(), $CurrentTable->GetColumns());
            
            foreach($TraitsToRemove as $TraitToRemove) {
                $Modifier->DropTableTrait($Connection, $Table, $TraitToRemove);
            }
            
            if($this->DropUnspecifiedColumns) {
                foreach($ColumnsToRemove as $ColumnToRemove) {
                    $Modifier->DropColumn($Connection, $Table, $ColumnToRemove);
                }
            }
            
            ksort($ColumnsToAdd, SORT_NUMERIC);
            ksort($ColumnsToModify, SORT_NUMERIC);
            $AllColumns = array_values($Table->GetColumns());
            $MinimumPositionIndex = min(array_keys($AllColumns));
            
            foreach($ColumnsToAdd as $Position => $ColumnToAdd) {
                $PreviousColumn = null;
                if($Position !== $MinimumPositionIndex)
                    $PreviousColumn = $AllColumns[$Position - 1];
                    
                $Modifier->AddColumn($Connection, $Table, $ColumnToAdd, $PreviousColumn);
            }            
            
            foreach($ColumnsToModify as $Position => $ColumnToModify) {
                $PreviousColumn = null;
                if($Position !== $MinimumPositionIndex)
                    $PreviousColumn = $AllColumns[$Position - 1];
                
                $Modifier->ModifyColumn($Connection, $Table, $ColumnToModify, $PreviousColumn);
            }
            
            foreach($TraitsToAdd as $TraitToAdd) {
                $Modifier->AddTableTrait($Connection, $Table, $TraitToAdd);
            }
        }
    }
    
    private function CreateTable(IConnection $Connection, IDatabaseModifier $Modifier, Relational\Table $Table) {
        $Modifier->CreateTableStructure($Connection, $Table);
    }
    
    private function DropRelationalTraits(IConnection $Connection, 
            IDatabaseModifier $Modifier, Relational\Database $Database) {
        foreach($Database->GetTables() as $Table) {
            foreach($Table->GetRelationalTraits() as $Trait) {
                $Modifier->DropTableTrait($Connection, $Table, $Trait);
            }
        }
    }
    
    private function AddRelationalTraits(IConnection $Connection, 
            IDatabaseModifier $Modifier, Relational\Database $Database) {
        foreach($Database->GetTables() as $Table) {
            foreach($Table->GetRelationalTraits() as $Trait) {
                $Modifier->AddTableTrait($Connection, $Table, $Trait);
            }
        }
    }
    
    private function GetTableStructuralTraits(Relational\Table $Table, Relational\Table $CurrentTable) {
        $Traits = $Table->GetStructuralTraits();
        $CurrentTraits = $CurrentTable->GetStructuralTraits();
        
        $TraitsToAdd = [];
        foreach($Traits as $Trait) {
            if(count(array_filter($CurrentTraits, [$Trait, 'Is'])) === 0)
                $TraitsToAdd[] = $Trait;
        }
        $TraitsToRemove = [];
        foreach($CurrentTraits as $CurrentTrait) {
            if(count(array_filter($Traits, [$CurrentTrait, 'Is'])) === 0)
                $TraitsToRemove[] = $CurrentTrait;
        }

        return [$TraitsToAdd, $TraitsToRemove];
    }
    
    private function GetTableColumns(array $Columns, array $CurrentColumns) {
        $ColumnNamesMap = array_flip(array_keys($Columns));
        $CurrentColumnNamesMap = array_flip(array_keys($CurrentColumns));

        $ColumnNamesToModify = array_intersect_key($CurrentColumnNamesMap, $ColumnNamesMap);
        
        $ColumnsToModify = [];
        foreach($ColumnNamesToModify as $ColumnName => $CurrentPosition) {
            $Position = $ColumnNamesMap[$ColumnName];
            
            if($Columns[$ColumnName]->Is($CurrentColumns[$ColumnName]) 
                    && $Position === $CurrentPosition)
                continue;
            
            $ColumnsToModify[$Position] = $Columns[$ColumnName];
        }
        
        $ColumnNamesToAdd = array_diff_key($ColumnNamesMap, $CurrentColumnNamesMap);
        $ColumnsToAdd = [];
        foreach($ColumnNamesToAdd as $ColumnName => $Position) {
            $ColumnsToAdd[$Position] = $Columns[$ColumnName];
        }
        
        $ColumnNamesToRemove = array_diff_key($CurrentColumnNamesMap, $ColumnNamesMap);
        $ColumnsToRemove = [];
        foreach($ColumnNamesToRemove as $ColumnName => $Position) {
            $ColumnsToRemove[$Position] = $CurrentColumns[$ColumnName];
        }
        

        return [$ColumnsToModify, $ColumnsToAdd, $ColumnsToRemove];
    }
}

?>