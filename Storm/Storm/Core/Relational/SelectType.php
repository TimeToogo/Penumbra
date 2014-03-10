<?php

namespace Storm\Core\Relational;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SelectType {
    private function __construct() { }
    
    const ResultSet = 0;
    const Data = 1;
    const Exists = 2;
}

?>