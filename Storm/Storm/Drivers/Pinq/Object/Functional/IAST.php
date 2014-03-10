<?php

namespace Storm\Drivers\Pinq\Object\Functional;

use \Storm\Core\Object\Expressions\Expression;

interface IAST {
    /**
     * @return Expression[]
     */
    public function GetExpressions();
}

?>
