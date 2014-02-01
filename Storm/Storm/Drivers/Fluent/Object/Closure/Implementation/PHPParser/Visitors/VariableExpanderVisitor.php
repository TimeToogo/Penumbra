<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser\Visitors;

class VariableExpanderVisitor extends \PHPParser_NodeVisitorAbstract {
    private $Traverser;
    private $VariableExpressionMap = array();
    
    function __construct(\PHPParser_NodeTraverserInterface $Traverser) {
        $this->Traverser = $Traverser;
    }
    
    private function VerifyVariableNode(\PHPParser_Node_Expr_Variable $Node) {
        if(!is_string($Node->name)) {
            throw new \Exception('Variable expander does not support variable variables');
        }
    }
    
    public function enterNode(\PHPParser_Node $Node) {
        $NodeType = str_replace('PHPParser_Node_Expr_', '', get_class($Node));

        switch (true) {
            case strpos($Node->getType(), 'Expr_Assign') === 0:
                if($Node->var instanceof \PHPParser_Node_Expr_Variable) {
                    $this->VerifyVariableNode($Node->var);
                    $Name = $Node->var->name;
                    
                    $AssignmentValue = $this->RecursiveExpandNode($this->AssignmentToExpressionNode($Node, $NodeType));
                    $this->VariableExpressionMap[$Name] = $AssignmentValue;
                    
                    return $Node->var;//Will be replace on leaveNode
                }
                break;
                
            default:
                return;
        }
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $this->VerifyVariableNode($Node);
                $Name = $Node->name;
                
                if(isset($this->VariableExpressionMap[$Name])) {
                    return $this->VariableExpressionMap[$Name];
                }
            
            default:
                return;
        }
    }
    
    private function RecursiveExpandNode(\PHPParser_Node $Node) {
        if($Node instanceof \PHPParser_Node_Expr_Variable) {
            return $Node;
        }
        else {
            return $this->Traverser->traverse([$Node])[0];
        }
    }
    
    private static $AssigmentToBinaryNodeMap = [
        'AssignBitwiseAnd' => 'BitwiseAnd',
        'AssignBitwiseOr' => 'BitwiseOr',
        'AssignBitwiseXor' => 'BitwiseXor',
        'AssignConcat' => 'Concat',
        'AssignDiv' => 'Div',
        'AssignMinus' => 'Minus',
        'AssignMod' => 'Mod',
        'AssignMul' => 'Mul',
        'AssignPlus' => 'Plus',
        'AssignShiftLeft' => 'ShiftLeft',
        'AssignShiftRight' => 'ShiftRight',
    ];
    
    private function AssignmentToExpressionNode(\PHPParser_Node_Expr $Node, $NodeType) {
        if(!isset(self::$AssigmentToBinaryNodeMap[$NodeType])) {
            return $Node->expr;
        }
        else {
            $this->VerifyVariableNode($Node->var);
            $Name = $Node->var->name;
            $BinaryExpresiionNodeType = '\PHPParser_Node_Expr_' . self::$AssigmentToBinaryNodeMap[$NodeType];
            $CurrentExpression = isset($this->VariableExpressionMap[$Name]) ? 
                    $this->VariableExpressionMap[$Name] : $Node->var;
            return new $BinaryExpresiionNodeType($CurrentExpression, $Node->expr);
        }
    }
}

?>
