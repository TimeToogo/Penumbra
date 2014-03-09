<?php

namespace Storm\Drivers\Pinq\Object\Functional\Walkers;

use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Object\Expressions\Operators;

/**
 * Resolves all resolvable variables within the expression tree.
 * 
 * {
 *     $Var = 4 + 5 - $Unresolvable;
 *     return 3 + $Var;
 * }
 * === resolves to ===
 * {
 *     4 + 5;
 *     return 3 + (4 + 5 - $Unresolvable)
 * }
 * === with ['Unresolvable' => 97] resolves to ===
 * {
 *     4 + 5;
 *     return 3 + (4 + 5 - 97)
 * }
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class VariableResolverWalker extends O\ExpressionWalker {
    private $UnresolvedVariables = [];
    private $VariableExpressionMap = [];
    
    public function HasUnresolvedVariables() {
        return count($this->UnresolvedVariables) > 0;
    }
    
    public function GetUnresolvedVariables() {
        return $this->UnresolvedVariables === 0;
    }
    
    public function ResetUnresolvedVariables() {
        $this->UnresolvedVariables = [];
    }
    
    public function SetVariableValueMap(array $VariableValueMap) {
        foreach($VariableValueMap as $VariableName => $Value) {
            $this->VariableExpressionMap[$VariableName] = O\Expression::Value($Value);
        }
    }
    
    private static $AssignmentToBinaryOperator = [
        Operators\Assignment::Addition => Operators\Binary::Addition,
        Operators\Assignment::BitwiseAnd => Operators\Binary::BitwiseAnd,
        Operators\Assignment::BitwiseOr => Operators\Binary::BitwiseOr,
        Operators\Assignment::BitwiseXor => Operators\Binary::BitwiseXor,
        Operators\Assignment::Concatenate => Operators\Binary::Concatenation,
        Operators\Assignment::Division => Operators\Binary::Division,
        Operators\Assignment::Modulus => Operators\Binary::Modulus,
        Operators\Assignment::Multiplication => Operators\Binary::Multiplication,
        Operators\Assignment::ShiftLeft => Operators\Binary::ShiftLeft,
        Operators\Assignment::ShiftRight => Operators\Binary::ShiftRight,
        Operators\Assignment::Subtraction => Operators\Binary::Subtraction,
    ];
    private function AssignmentToBinaryOperator($AssignmentOperator) {
        return isset(self::$AssignmentToBinaryOperator[$AssignmentOperator]) ?
                self::$AssignmentToBinaryOperator[$AssignmentOperator] : null;
    }
    
    public function WalkAssignment(O\AssignmentExpression $Expression) {
        $AssignToExpression = $this->Walk($Expression->GetAssignToExpression())->Simplify();
        $AssignmentOperator = $Expression->GetOperator();
        $AssignmentValueExpression = $this->Walk($Expression->GetAssignmentValueExpression());
        
        if($AssignToExpression instanceof O\UnresolvedVariableExpression
                && $AssignToExpression->GetNameExpression() instanceof O\ValueExpression) {
            $AssignmentName = $AssignToExpression->GetValue();
            $BinaryOperator = $this->AssignmentToBinaryOperator($AssignmentOperator);
            
            if($BinaryOperator !== null) {
                $CurrentValueExpression = isset($this->VariableExpressionMap[$AssignmentName]) ?
                        $this->VariableExpressionMap[$AssignmentName] : O\Expression::Value(null);
                
                $VariableValueExpression = 
                        O\Expression::BinaryOperation(
                                $CurrentValueExpression, 
                                $BinaryOperator, 
                                $AssignmentValueExpression);
                
            }
            else {
                $VariableValueExpression = $AssignmentValueExpression;
            }
            
            $this->VariableExpressionMap[$AssignmentName] = $VariableValueExpression;
            return $VariableValueExpression;
        }
        
        return $Expression->Update(
                $AssignToExpression, 
                $AssignmentOperator, 
                $AssignmentValueExpression);
    }
    
    public function WalkUnresolvedValue(O\UnresolvedVariableExpression $Expression) {
        $NameExpression = $this->Walk($Expression->GetNameExpression())->Simplify();
        if($NameExpression instanceof O\ValueExpression) {
            $Name = $NameExpression->GetValue();
            if(isset($this->VariableExpressionMap[$Name])) {
                return $this->VariableExpressionMap[$Name];
            }
            $this->UnresolvedVariables[] = $Name;
        }
        else {
            $this->UnresolvedVariables[] = $this->GetUnresolvedName($NameExpression);
        }
        
        return $Expression;
    }
    
    private function GetUnresolvedName(O\Expression $NameExpression) {
        if($NameExpression instanceof O\ValueExpression) {
            return $NameExpression->GetValue();
        }
        else if($NameExpression instanceof O\UnresolvedVariableExpression) {
            return '$' . $this->GetUnresolvedName($NameExpression);
        }
        else {
            return '{COMPLEX UNRESOLVED NAME}';
        }
    }
}

?>