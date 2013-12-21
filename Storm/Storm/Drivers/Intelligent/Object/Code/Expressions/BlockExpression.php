<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class BlockExpression extends MultipleExpression {
    public function __construct(array $Expressions) {
        parent::__construct($Expressions);
    }
    
    protected function CompileCode(&$Code) {
        $Code .= '{';
        parent::CompileCode($Code);
        $Code .= '}';
    }
}

?>