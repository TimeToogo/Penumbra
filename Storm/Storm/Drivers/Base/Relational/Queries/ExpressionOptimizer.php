<?php

 namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\ConstantExpression;
use \Storm\Core\Relational\Expressions\ColumnExpression;
use \Storm\Drivers\Base\Relational\Expressions\SetExpression;
use \Storm\Drivers\Base\Relational\Expressions\UnaryOperationExpression;
use \Storm\Drivers\Base\Relational\Expressions\BinaryOperationExpression;
use \Storm\Drivers\Base\Relational\Expressions\ValueListExpression;
use \Storm\Drivers\Base\Relational\Expressions\FunctionCallExpression;
use \Storm\Drivers\Base\Relational\Expressions\CompoundBooleanExpression;
 
abstract class ExpressionOptimizer implements IExpressionOptimizer {
    
    public function Optimize(CoreExpression $Expression) {
        switch (true) {
            case $Expression instanceof SetExpression:
                return $this->OptimizeSetExpression($Expression);
                
            case $Expression instanceof BinaryOperationExpression:
                return $this->OptimizeBinaryOperationExpression($Expression);
            
            case $Expression instanceof UnaryOperationExpression:
                return $this->OptimizeUnaryOperationExpression($Expression);
            
            case $Expression instanceof FunctionCallExpression:
                return $this->OptimizeFunctionCallExpression($Expression);
            
            default:
                return $Expression;
        }
    }

    protected function OptimizeSetExpression(SetExpression $Expression) {
        return $Expression;
    }
    
    protected function OptimizeBinaryOperationExpression(BinaryOperationExpression $Expression) {
        $LeftOperand = $Expression->GetLeftOperandExpression();
        $RightOperand = $Expression->GetRightOperandExpression();
        
        if($LeftOperand instanceof ReviveColumnExpression && $this->IsExpressionConstant($RightOperand)) {
            $this->OptimizeReviveColumnCompareConstantExpression($LeftOperand, $RightOperand);
        }
        else if($RightOperand instanceof ReviveColumnExpression && $this->IsExpressionConstant($LeftOperand)) {
            $this->OptimizeReviveColumnCompareConstantExpression($RightOperand, $LeftOperand);
        }
        else {
            return $Expression;
        }
        
        return Expression::BinaryOperation($LeftOperand, $Expression->GetOperator(), $RightOperand);
    }
    private function OptimizeReviveColumnCompareConstantExpression(ReviveColumnExpression &$ColumnExpression, CoreExpression &$ConstantExpression) {
        $Column = $ColumnExpression->GetColumn();
        $ColumnExpression = Expression::Column($Column);
        $ConstantExpression = Expression::PersistData($Column, $ConstantExpression);
    }
    
    final protected function AreExpressionsConstant(array $Expressions) {
        foreach($Expressions as $Expression) {
            if(!$this->IsExpressionConstant($Expression)) {
                return false;
            }
        }
        return true;
    }
    
    final protected function IsExpressionConstant(CoreExpression $Expression) {
        switch (true) {
            case $Expression instanceof ConstantExpression:
                return true;
                
            case $Expression instanceof ColumnExpression:
                return false;
            
            case $Expression instanceof UnaryOperationExpression:
                return $this->IsExpressionConstant($Expression->GetOperandExpression());
            
            case $Expression instanceof BinaryOperationExpression:
                return $this->IsExpressionConstant($Expression->GetLeftOperandExpression())
                        && $this->IsExpressionConstant($Expression->GetRightOperandExpression());
            
            case $Expression instanceof CompoundBooleanExpression:
                return $this->AreExpressionsConstant($Expression->GetBooleanExpressions());
            
            case $Expression instanceof FunctionCallExpression:
                return $this->AreExpressionsConstant($Expression->GetArgumentValueListExpression()->GetValueExpressions());
            
            default:
                return false;
        }
    }

    protected function OptimizeUnaryOperationExpression(UnaryOperationExpression $Expression) {
        return $Expression;
    }

    protected function OptimizeFunctionCallExpression(FunctionCallExpression $Expression) {
        return $Expression;
    }
}

?>