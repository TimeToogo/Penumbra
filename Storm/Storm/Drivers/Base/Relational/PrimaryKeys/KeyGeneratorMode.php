<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

final class KeyGeneratorMode {
    private function __construct() { }
    
    const PreInsert = 0;
    const ValueWithReturningData = 1;
    const PostInsert = 2;
}

?>
