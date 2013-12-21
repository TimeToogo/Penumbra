<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

abstract class StatementExpression extends Expression {
    final public function CompileCode(&$Code) {
        $this->CompileStatement($Code);
        $Code .= ';';
    }
    protected abstract function CompileStatement(&$Code);
    
    /**
     * @return BlockExpression
     */
    final public function AsBlock() {
        return new BlockExpression([$this]);
    }
}

?>