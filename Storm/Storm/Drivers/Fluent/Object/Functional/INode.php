<?php

namespace Storm\Drivers\Fluent\Object\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

interface INode {
    const Statement = 0;
    const Expression = 1;
    const Value = 1;
    const Other = 2;
    
    public function GetType();
    
    /**
     * @return INode[]
     */
    public function GetSubNodes();
}

?>
