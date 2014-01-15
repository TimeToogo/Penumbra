<?php

namespace Storm\Drivers\Base\Relational\Syncing;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\TableTrait;
use \Storm\Drivers\Base\Relational\Columns\ColumnTrait;
use \Storm\Drivers\Base\Relational\Columns\DataType;

abstract class DatabaseModifier implements IDatabaseModifier {
    private $TableTraitManger;
    private $ColumnTraitManager;
    
    public function __construct(
            Traits\ITableTraitManager $TableTraitManger, 
            Traits\IColumnTraitManager $ColumnTraitManager) {
        $this->TableTraitManger = $TableTraitManger;
        $this->ColumnTraitManager = $ColumnTraitManager;
    }    
    
    
    final public function CreateTableStructure(IConnection $Connection, Table $Table) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendCreateTableStructureQuery($QueryBuilder, 
                $this->TableTraitManger, $this->ColumnTraitManager,
                $Table);
        $QueryBuilder->Build()->Execute();
    }
    protected abstract function AppendCreateTableStructureQuery(QueryBuilder $QueryBuilder,
            Traits\ITableTraitManager $TableTraitManager, Traits\IColumnTraitManager $ColumnTraitManager,
            Table $Table);

    final public function DropTable(IConnection $Connection, Table $Table) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendDropTableQuery($QueryBuilder, $Table);
        $QueryBuilder->Build()->Execute();
    }
    protected abstract function AppendDropTableQuery(QueryBuilder $QueryBuilder, Table $Table);
    
    final public function AddTableTrait(IConnection $Connection, Table $Table, TableTrait $Trait) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->TableTraitManger->AppendAdd($Connection, $QueryBuilder, $Table, $Trait);
        $QueryBuilder->Build()->Execute();
    }
    
    final public function DropTableTrait(IConnection $Connection, Table $Table, TableTrait $Trait) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->TableTraitManger->AppendDrop($Connection, $QueryBuilder, $Table, $Trait);
        $QueryBuilder->Build()->Execute();
    }

    final public function AddColumn(IConnection $Connection, Table $Table, Column $Column, Column $PreviousColumn = null) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendAddColumnQuery($QueryBuilder, $Table, $this->ColumnTraitManager, $Column, $PreviousColumn);
        $QueryBuilder->Build()->Execute();
    }
    protected abstract function AppendAddColumnQuery(QueryBuilder $QueryBuilder, Table $Table, 
            Traits\IColumnTraitManager $ColumnTraitManager,
            Column $Column, Column $PreviousColumn = null);

    final public function ModifyColumn(IConnection $Connection, Table $Table, Column $Column, Column $PreviousColumn = null) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendModifyColumnQuery($QueryBuilder, $Table, $this->ColumnTraitManager, $Column, $PreviousColumn);
        $QueryBuilder->Build()->Execute();
    }
    protected abstract function AppendModifyColumnQuery(QueryBuilder $QueryBuilder, Table $Table, 
            Traits\IColumnTraitManager $ColumnTraitManager,
            Column $Column, Column $PreviousColumn = null);
    
    final public function DropColumn(IConnection $Connection, Table $Table, Column $Column) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendDropColumnQuery($QueryBuilder, $Table, $Column);
        $QueryBuilder->Build()->Execute();
    }
    protected abstract function AppendDropColumnQuery(QueryBuilder $QueryBuilder, Table $Table, Column $Column);
}

?>
