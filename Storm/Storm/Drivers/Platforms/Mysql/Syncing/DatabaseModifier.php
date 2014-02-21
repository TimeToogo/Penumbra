<?php

namespace Storm\Drivers\Platforms\Mysql\Syncing;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Syncing;
use \Storm\Drivers\Base\Relational\Syncing\Traits\IColumnTraitManager;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;
use \Storm\Drivers\Platforms\Mysql\Tables;
use \Storm\Drivers\Platforms\Mysql\QueryBuilderExtensions;

class DatabaseModifier extends Syncing\DatabaseModifier {
    public function __construct() {
        parent::__construct(new TableTraitManager(), new ColumnTraitManager());
    }
    
    protected function AppendCreateTableStructureQuery(QueryBuilder $QueryBuilder, 
            Syncing\Traits\ITableTraitManager $TableTraitManager, IColumnTraitManager $ColumnTraitManager,
            Table $Table) {
        
        $QueryBuilder->AppendIdentifier('CREATE TABLE # ', [$Table->GetName()]);
        $QueryBuilder->Append('(');
        $First = true;
        foreach($Table->GetColumns() as $Column) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(',');
            
            $this->AppendFullColumn($QueryBuilder, $ColumnTraitManager, $Column, null , false);
        }
        $TableOptions = [];
        foreach($Table->GetStructuralTraits() as $StructuralTableTrait) {
            if($StructuralTableTrait instanceof Tables\CharacterSet
                    || $StructuralTableTrait instanceof Tables\Collation
                    || $StructuralTableTrait instanceof Tables\Engine
                    || $StructuralTableTrait instanceof Relational\Traits\Comment) {
                $TableOptions[] = $StructuralTableTrait;
            }
            else {
                $QueryBuilder->Append(',');
                $TableTraitManager->AppendDefinition($QueryBuilder, $Table, $StructuralTableTrait);
            }
        }
        $QueryBuilder->Append(')');
        foreach($TableOptions as $TableOption) {
            $QueryBuilder->Append(',');
            $TableTraitManager->AppendDefinition($QueryBuilder, $Table, $TableOption);
        }
    }
    
    protected function AppendCreateTableColumnsQuery(QueryBuilder $QueryBuilder, Table $Table) {
        $QueryBuilder->AppendIdentifier('CREATE TABLE # ', [$Table->GetName()]);
        $QueryBuilder->Append('(');
        $First = true;
        foreach($Table->GetColumns() as $Column) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(',');
            
            $this->AppendBlankColumn($QueryBuilder, $Column);
        }
        $QueryBuilder->Append(')');
    }

    protected function AppendDropTableQuery(QueryBuilder $QueryBuilder, Table $Table) {
        $QueryBuilder->AppendIdentifier('DROP TABLE #', [$Table->GetName()]);
    }

    protected function AppendAddColumnQuery(QueryBuilder $QueryBuilder, Table $Table, IColumnTraitManager $ColumnTraitManager, Column $Column, Column $PreviousColumn = null) {
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->Append('ADD COLUMN ');
        $this->AppendFullColumn($QueryBuilder, $ColumnTraitManager, $Column, $PreviousColumn);
    }

    protected function AppendModifyColumnQuery(QueryBuilder $QueryBuilder, Table $Table, IColumnTraitManager $ColumnTraitManager, Column $Column, Column $PreviousColumn = null) {
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->Append('MODIFY COLUMN ');
        $this->AppendFullColumn($QueryBuilder, $ColumnTraitManager, $Column, $PreviousColumn);
    }
    
    protected function AppendDropColumnQuery(QueryBuilder $QueryBuilder, Table $Table, Column $Column) {
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->AppendIdentifier('DROP COLUMN #', [$Column->GetName()]);
    }

    // <editor-fold defaultstate="collapsed" desc="Column Definition Helpers">
    private function AppendBlankColumn(QueryBuilder $QueryBuilder, 
            Column $Column) {
        $QueryBuilder->AppendIdentifier('#', [$Column->GetName()]);
        $QueryBuilder->Append(' ');
        $this->AppendDataTypeDefinition($QueryBuilder, $Column->GetDataType());
    }
    
    private function AppendFullColumn(QueryBuilder $QueryBuilder, IColumnTraitManager $ColumnTraitManager, 
            Column $Column, Column $PreviousColumn = null, $IncludePosition = true) {
        $QueryBuilder->AppendIdentifier('#', [$Column->GetName()]);
        $QueryBuilder->Append(' ');
        $this->AppendColumnDefinition($QueryBuilder, $ColumnTraitManager, $Column);
        if($IncludePosition) {
            $QueryBuilder->Append(' ');
            $this->AppendColumnPosition($QueryBuilder, $PreviousColumn);
        }
    }

    private function AppendColumnDefinition(QueryBuilder $QueryBuilder, IColumnTraitManager $ColumnTraitManager, Column $Column) {
        $this->AppendDataTypeDefinition($QueryBuilder, $Column->GetDataType());
        foreach ($Column->GetTraits() as $Trait) {
            $QueryBuilder->Append(' ');
            $ColumnTraitManager->AppendDefinition($QueryBuilder, $Trait);
        }
    }

    private function AppendDataTypeDefinition(QueryBuilder $QueryBuilder, DataType $DataType) {
        $QueryBuilder->Append($DataType->GetDataType());
        $Parameters = $DataType->GetParameters();
        if (count($Parameters) > 0) {
            $QueryBuilder->AppendAllEscaped('(#)', $Parameters, ',');
        }
        if ($DataType->GetExtra() !== null) {
            $QueryBuilder->Append(' ' . $DataType->GetExtra());
        }
    }

    private function AppendColumnPosition(QueryBuilder $QueryBuilder, Column $PreviousColumn = null) {
        return $PreviousColumn === null ?
                $QueryBuilder->Append('FIRST') :
                $QueryBuilder->AppendIdentifier('AFTER #', [$PreviousColumn->GetName()]);
    }
    // </editor-fold>

}

?>