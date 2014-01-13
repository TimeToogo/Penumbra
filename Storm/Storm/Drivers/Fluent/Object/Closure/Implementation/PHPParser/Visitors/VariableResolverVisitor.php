<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\Visitors;

use \Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\PHPParserConstantValueNode;

class VariableResolverVisitor extends \PHPParser_NodeVisitorAbstract {
    private $VariableValueMap = array();
    
    public function SetVariableValueMap(array $VariableValueMap) {
        $this->VariableValueMap = $VariableValueMap;
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = $Node->name;
                if(!is_string($Name)) {
                    throw new \Exception('Variable resolver does not support variable variables');
                }
                if(isset($this->VariableValueMap[$Name])) {
                    return new PHPParserConstantValueNode($this->VariableValueMap[$Name]);
                }
        }
    }
}

?>
