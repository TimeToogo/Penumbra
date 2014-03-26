<?php

namespace Penumbra\Pinq\Functional;

use \Penumbra\Core\Object\Expressions\Expression;

interface IAST {
    /**
     * @return Expression[]
     */
    public function GetExpressions();
}

?>
