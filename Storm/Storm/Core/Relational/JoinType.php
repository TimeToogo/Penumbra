<?php

namespace Storm\Core\Relational;

/**
 * A type of join
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class JoinType {
    private function __construct() {}
    
    const Inner = 1;
    const Left = 2;
    const Right = 3;
    const Full = 4;
    const Cross = 5;
    
    public static function IsValid($JoinType) {
        return in_array($JoinType, [self::Inner, self::Left, self::Right, self::Full, self::Cross]);
    }
}

?>