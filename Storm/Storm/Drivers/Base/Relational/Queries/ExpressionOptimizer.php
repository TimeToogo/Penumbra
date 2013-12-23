<?php

 namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\ConstantExpression;
use \Storm\Core\Relational\Expressions\ColumnExpression;
use \Storm\Drivers\Base\Relational\Expressions\SetExpression;
use \Storm\Drivers\Base\Relational\Expressions\UnaryOperationExpression;
use \Storm\Drivers\Base\Relational\Expressions\BinaryOperationExpression;
use \Storm\Drivers\Base\Relational\Expressions\ValueListExpression;
use \Storm\Drivers\Base\Relational\Expressions\FunctionCallExpression;
use \Storm\Drivers\Base\Relational\Expressions\ReviveColumnExpression;
use \Storm\Drivers\Base\Relational\Expressions\PersistDataExpression;
 
abstract class ExpressionOptimizer implements IExpressionOptimizer {
    
    public function Optimize(CoreExpression $Expression) {
        switch ($Expression->GetType()) {
            case SetExpression::GetType():
                return $this->OptimizeSetExpression($Expression);
                
            case BinaryOperationExpression::GetType():
                return $this->OptimizeBinaryOperationExpression($Expression);
            
            case UnaryOperationExpression::GetType():
                return $this->OptimizeUnaryOperationExpression($Expression);
            
            case FunctionCallExpression::GetType():
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
    
    final protected function IsExpressionConstant(CoreExpression $Expression) {
        if($Expression instanceof ConstantExpression) {
            return true;
        }
        if($Expression instanceof ColumnExpression) {
            return false;
        }
        if($Expression instanceof UnaryOperationExpression) 
            return $this->IsExpressionConstant($Expression->GetOperandExpression());
        
        if($Expression instanceof FunctionCallExpression) {
            foreach($Expression->GetArgumentValueListExpression()->GetValueExpressions() as $ArgumentExpression) {
                if(!$this->IsExpressionConstant($ArgumentExpression))
                    return false;
            }
            return true;
        }
        
        if($Expression instanceof BinaryOperationExpression) {
            return $this->IsExpressionConstant($Expression->GetLeftOperandExpression())
                    && $this->IsExpressionConstant($Expression->GetRightOperandExpression());
        }
        
        return false;
    }

    protected function OptimizeUnaryOperationExpression(UnaryOperationExpression $Expression) {
        return $Expression;
    }

    protected function OptimizeFunctionCallExpression(FunctionCallExpression $Expression) {
        return $Expression;
    }
}

?>