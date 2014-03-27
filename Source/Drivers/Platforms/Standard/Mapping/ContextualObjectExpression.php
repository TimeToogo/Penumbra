<?php

namespace Penumbra\Drivers\Platforms\Standard\Mapping;

use \Penumbra\Drivers\Base\Relational\Expressions as R;

class ContextualObjectExpression extends R\Expression {
    private $Object;
    
    public function __construct($Object) {
        $this->Object = $Object;
    }
    
    final public function GetObject() {
        return $this->Object;
    }

    
    public function Traverse(R\ExpressionWalker $Walker) {
        
    }
}

?>