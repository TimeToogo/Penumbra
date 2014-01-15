<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\Visitors;

use \Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\PHPParserConstantValueNode;
use \Storm\Drivers\Fluent\Object\Properties\Accessors;

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
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            //Field
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
                $Name = $Node->name;
                if(!is_string($Name)) {
                    throw new \Exception('Cannot build accessor: Property fetch cannot be dynamic');
                }
                $this->AccessorBuilder->$Name;
                break;
            
            //Method
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                $Name = $Node->name;
                if(!is_string($Name)) {
                    throw new \Exception('Cannot build accessor: Method call cannot be dynamic');
                }
                call_user_func_array([$this->AccessorBuilder, $Name], $this->GetNodeValues($Node->args));
                break;
                
            //Indexor
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                $this->AccessorBuilder[$this->GetNodeValue($Node->dim)];
                break;
                
            //Invocation
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                if($Node->name instanceof \PHPParser_Node_Expr_Variable && 
                        $Node->name->name === $this->EntityVariableName) {
                    call_user_func_array($this->AccessorBuilder, $this->GetNodeValues($Node->args));
                }
                break;
            
            default:
                return;
        }
    }
    
    public function GetNodeValues(array $ArgumentNodes) {
        return array_map([$this, 'GetNodeValue'], $ArgumentNodes);
    }
    
    public function GetNodeValue(\PHPParser_Node $Node) {
        if($Node instanceof PHPParserConstantValueNode) {
            $Node->Value;
        }
        else {
            throw new \Exception('Cannot ');
        }
    }
}

?>
