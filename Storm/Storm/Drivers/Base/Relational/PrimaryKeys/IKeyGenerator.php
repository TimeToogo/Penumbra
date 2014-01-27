<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Relational\Table;

interface IKeyGenerator {
    public function GetKeyGeneratorType();
    public function SetTable(Table $Table);
}

?>
