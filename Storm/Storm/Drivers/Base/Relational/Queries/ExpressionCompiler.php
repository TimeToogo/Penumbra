<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Expressions as CoreE;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Core\Relational\Expressions\Expression;

abstract class ExpressionCompiler implements IExpressionCompiler {
    private $ExpressionOptimizer;
    public function __construct(IExpressionOptimizer $ExpressionOptimizer) {
        $this->ExpressionOptimizer = $ExpressionOptimizer;
    }
    
    final public function Append(QueryBuilder $QueryBuilder, Expression $Expression) {
        $Expression = $this->ExpressionOptimizer->Optimize($Expression);
        
        switch ($Expression->GetType()) {
            case E\ReviveColumnExpression::GetType():
                return $this->Append($QueryBuilder, $Expression->GetReviveExpression());
                
            case E\PersistDataExpression::GetType():
                return $this->Append($QueryBuilder, $Expression->GetPersistExpression());
                
            case CoreE\ColumnExpression::GetType():
                return $this->AppendColumn($QueryBuilder, $Expression);
                
            case CoreE\ConstantExpression::GetType():
                return $this->AppendConstant($QueryBuilder, $Expression);
                
            case E\SetExpression::GetType():
                return $this->AppendSet($QueryBuilder, $Expression);
                
            case E\BinaryOperationExpression::GetType():
                return $this->AppendBinaryOperation($QueryBuilder, $Expression);
                
            case E\UnaryOperationExpression::GetType():
                return $this->AppendUnaryOperation($QueryBuilder, $Expression);
                
            case E\CastExpression::GetType():
                return $this->AppendCast($QueryBuilder, $Expression);
                
            case E\FunctionCallExpression::GetType():
                return $this->AppendFunctionCall($QueryBuilder, $Expression);
                
            case E\KeywordExpression::GetType():
                return $this->AppendKeyword($QueryBuilder, $Expression);
                                
            case E\ValueListExpression::GetType():
                return $this->AppendList($QueryBuilder, $Expression);

            default:
                throw new \Exception();
        }
    }
    
    protected function AppendColumn(QueryBuilder $QueryBuilder, CoreE\ColumnExpression $Expression) {
        $Column = $Expression->GetColumn();
        $QueryBuilder->AppendColumn('#', $Column);
    }

    protected function AppendConstant(QueryBuilder $QueryBuilder, CoreE\ConstantExpression $Expression) {
        $QueryBuilder->AppendValue('#', $Expression->GetValue());
    }

    protected function AppendBinaryOperation(QueryBuilder $QueryBuilder, E\BinaryOperationExpression $Expression) {
        $this->Append($QueryBuilder, $Expression->GetLeftOperandExpression());
        $QueryBuilder->Append($this->GetBinaryOperatorString($Expression->GetOperator()));
        $this->Append($QueryBuilder, $Expression->GetRightOperandExpression());
    }
    protected abstract function GetBinaryOperatorString($Operator);

    protected function AppendUnaryOperation(QueryBuilder $QueryBuilder, E\UnaryOperationExpression $Expression) {
        $QueryBuilder->Append($this->GetUnaryOperatorString($Expression->GetOperator()));
        $this->Append($QueryBuilder, $Expression->GetOperandExpression());
    }
    protected abstract function GetUnaryOperatorString($Operator);

    protected function AppendKeyword(QueryBuilder $QueryBuilder, E\KeywordExpression $Expression) {
        $QueryBuilder->Append($Expression->GetKeyword());
    }

    protected abstract function AppendCast(QueryBuilder $QueryBuilder, E\CastExpression $Expression);

    protected abstract function AppendFunctionCall(QueryBuilder $QueryBuilder, E\FunctionCallExpression $Expression);

    protected abstract function AppendList(QueryBuilder $QueryBuilder, E\ValueListExpression $Expression);

    protected function AppendSet(QueryBuilder $QueryBuilder, E\SetExpression $Expression) {
        $this->Append($QueryBuilder, $Expression->GetLeftOperandExpression());
        $QueryBuilder->Append($this->GetSetOperatorString($Expression->GetOperator()));
        $this->Append($QueryBuilder, $Expression->GetRightOperandExpression());
    }
    protected abstract function GetSetOperatorString($Operator);
}

?>