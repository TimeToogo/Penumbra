<?php

namespace Storm\Drivers\Intelligent\Object\Closure\Implementation\PHPParser\Visitors;

use \Storm\Drivers\Intelligent\Object\Closure\Implementation\PHPParser\PHPParserConstantValueNode;

class ConstantValueNodeReplacerVisitor extends \PHPParser_NodeVisitorAbstract {
    
    public function leaveNode(\PHPParser_Node $Node) {
        $Value = null;
        switch (true) {                
            case $Node instanceof \PHPParser_Node_Scalar_DNumber:
            case $Node instanceof \PHPParser_Node_Scalar_LNumber:
            case $Node instanceof \PHPParser_Node_Scalar_String:
                $Value = $Node->value;
                break;
            
            case $Node instanceof \PHPParser_Node_Expr_ConstFetch:
                $Value = constant($Node->name);
                break;
                
            case $Node instanceof \PHPParser_Node_Expr_ClassConstFetch:
                $Value = constant($Node->class . '::' . $Node->name);
                break;
                        
            case $Node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                if(!($Node->class instanceof \PHPParser_Node_Name) || !is_string($Node->name)) {
                    throw new \Exception('Static property must not be expressions');
                }
                $ClassName = (string)$Node->class;
                $Value = $ClassName::${$Node->name};
                break;
                
            default:
                return;
        }
        
        return new PHPParserConstantValueNode($Value);
    }
}

?>
