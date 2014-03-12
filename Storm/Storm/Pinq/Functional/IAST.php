<?php

namespace Storm\Pinq\Functional;

use \Storm\Core\Object\Expressions\Expression;

interface IAST {
    /**
     * @return Expression[]
     */
    public function GetExpressions();
}

?>
