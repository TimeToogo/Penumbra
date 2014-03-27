<?php

namespace Penumbra\Drivers\Base\Relational\PrimaryKeys;

use \Penumbra\Core\Relational\ITable;

interface IKeyGenerator {
    public function GetKeyGeneratorType();
    public function SetTable(ITable $Table);
}

?>
