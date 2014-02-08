<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\Visitors;

use Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\AST;

class UnresolvedVariableVisitor extends \PHPParser_NodeVisitorAbstract {
    private $UnresolvedVariables;
    private $IgnoreVariables;
    
    public function __construct(array &$UnresolvedVariables, array $IgnoreVariables = array()) {
        $this->UnresolvedVariables =& $UnresolvedVariables;
        $this->IgnoreVariables = $IgnoreVariables;
    }
    
    public function beforeTraverse(array $Nodes) {
        $this->UnresolvedVariables = array();
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = AST::VerifyNameNode($Node->name);
                $this->UnresolvedVariables[$Name] = true;
        }
    }
    
    public function afterTraverse(array $Nodes) {
        $this->UnresolvedVariables = 
                array_diff(array_keys($this->UnresolvedVariables), $this->IgnoreVariables);
    }
}

?>
