<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\Visitors;

use \Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\PHPParserConstantValueNode;
use \Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\AST;

class VariableResolverVisitor extends \PHPParser_NodeVisitorAbstract {
    private $VariableValueMap = [];
    
    public function SetVariableValueMap(array $VariableValueMap) {
        $this->VariableValueMap = $VariableValueMap;
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = AST::VerifyNameNode($Node->name);
                if(isset($this->VariableValueMap[$Name])) {
                    return new PHPParserConstantValueNode($this->VariableValueMap[$Name]);
                }
        }
    }
}

?>
