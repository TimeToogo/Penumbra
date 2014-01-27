<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

final class KeyGeneratorType {
    private function __construct() { }
    
    const PreInsert = 0;
    const ReturningData = 1;
    const PostMultiInsert = 2;
    const PostIndividualInsert = 3;
}

?>
