<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullConnection implements Relational\Queries\IConnection {
    public function IsInTransaction() { }
    public function BeginTransaction() { }
    public function CommitTransaction() { } 
    public function RollbackTransaction() { }
    public function Execute($QueryString, Relational\Queries\Bindings $Bindings = null) { }
    public function Prepare($QueryString, Relational\Queries\Bindings $Bindings = null) { }
    public function FetchValue($QueryString, Relational\Queries\Bindings $Bindings = null) { }
    public function Disconnect() { }
    public function Escape($Value, $ParameterType) { }
    public function GetLastInsertIncrement() { }
    public function LoadRows(\Storm\Core\Relational\Table $Table, Relational\Queries\IQuery $Query) { }
    public function QueryBuilder(Relational\Queries\Bindings $Bindings = null) { }
    public function SetIdentifierEscaper(Relational\Queries\IIdentifierEscaper $IdentifierEscaper) { }
    public function LoadJoinedRows(array $JoinedTables, Relational\Queries\IQuery $Query) { }
}

?>