<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\Visitors;

use \Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\PHPParserConstantValueNode;
use \Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\AST;

class ConstantValueNodeReplacerVisitor extends \PHPParser_NodeVisitorAbstract {
    
    public function leaveNode(\PHPParser_Node $Node) {
        $IsConstant = null;
        $Value = $this->GetConstantValue($Node, $IsConstant);
        
        if($IsConstant) {
            return new PHPParserConstantValueNode($Value);
        }
    }
    
    private function GetConstantValue(\PHPParser_Node $Node, &$IsConstant) {
        switch (true) {
            case $Node instanceof PHPParserConstantValueNode:
                return $Node->Value;
            
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
                $Name = AST::VerifyNameNode($Node->class);
                $ClassName = (string)$Node->class;
                $Value = $ClassName::${$Node->name};
                break;
            
            case $Node instanceof \PHPParser_Node_Expr_Array:
                $Value = array();
                foreach ($Node->items as $Key => $Item) {
                    $IsKeyConstant = true;
                    $IsValueConstant = true;
                    $Key = $Item->key === null ? null : $this->GetConstantValue($Item->key, $IsKeyConstant);
                    $ItemValue = $this->GetConstantValue($Item->value, $IsValueConstant);
                    if(!$IsKeyConstant || !$IsValueConstant) {
                        $IsConstant = false;
                        return;
                    }
                    $Key !== null ? $Value[$Key] = $ItemValue : $Value[] = $ItemValue;
                }
                break;
                
            default:
                $IsConstant = false;
                return;
        }
        $IsConstant = true;
        return $Value;
    }
}

?>
