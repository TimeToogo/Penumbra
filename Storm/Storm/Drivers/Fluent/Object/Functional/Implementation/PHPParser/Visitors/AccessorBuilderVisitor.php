<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\Visitors;

use \Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\PHPParserConstantValueNode;
use \Storm\Drivers\Fluent\Object\Properties\Accessors;
use \Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\AST;

class AccessorBuilderVisitor extends \PHPParser_NodeVisitorAbstract {
    private $EntityVariableName;
    private $AccessorBuilder;
    
    public function __construct($EntityVariableName) {
        $this->EntityVariableName = $EntityVariableName;
    }
    
    public function beforeTraverse(array $Nodes) {
        $this->AccessorBuilder = new Accessors\Builder();
    }
    
    public function GetAccessor() {
        return $this->AccessorBuilder->GetAccessor();
    }
    
    public function enterNode(\PHPParser_Node $Node) {
        //Do not want to traverse arguments of any invocations/method calls
        $ClonedNode = clone $Node;
        unset($ClonedNode->args);
        return $ClonedNode;
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            //Field
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
                $Name = AST::VerifyNameNode($Node->name);
                $this->AccessorBuilder->$Name;
                break;
            
            //Method
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                $Name = AST::VerifyNameNode($Node->name);
                $this->AccessorBuilder->$Name();
                break;
                
            //Indexor
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                if(!($Node->dim instanceof PHPParserConstantValueNode)) {
                    throw new \Storm\Drivers\Fluent\Object\Functional\ASTException(
                            'Property indexor must be a constant value');
                }
                $this->AccessorBuilder[$this->GetNodeValue($Node->dim)];
                break;
                
            //Invocation
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                if($Node->name instanceof \PHPParser_Node_Expr_Variable && 
                        $Node->name->name === $this->EntityVariableName) {
                    $this->AccessorBuilder();
                }
                break;
            
            default:
                return;
        }
    }
}

?>
