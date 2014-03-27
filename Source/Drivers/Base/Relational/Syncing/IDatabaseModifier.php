<?php

namespace Penumbra\Drivers\Base\Relational\Syncing;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\Column;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

interface IDatabaseModifier {
    public function CreateTableStructure(IConnection $Connection, Relational\Table $Table);
    public function DropTable(IConnection $Connection, Relational\Table $Table);
    public function AddTableTrait(IConnection $Connection, Relational\Table $Table, Relational\TableTrait $Trait);
    public function DropTableTrait(IConnection $Connection, Relational\Table $Table, Relational\TableTrait $Trait);
    
    public function AddColumn(IConnection $Connection, Relational\Table $Table, Column $Column, Column $PreviousColumn = null);
    public function ModifyColumn(IConnection $Connection, Relational\Table $Table, Column $Column, Column $PreviousColumn = null);
    public function DropColumn(IConnection $Connection, Relational\Table $Table, Column $Column);
}

?>