<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Relational\ITable;

interface IKeyGenerator {
    public function GetKeyGeneratorType();
    public function SetTable(ITable $Table);
}

?>
