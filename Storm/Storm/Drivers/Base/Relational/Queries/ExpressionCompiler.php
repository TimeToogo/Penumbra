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
        
        switch (true) {
            case $Expression instanceof E\ReviveColumnExpression:
                return $this->Append($QueryBuilder, $Expression->GetReviveExpression());
                
            case $Expression instanceof E\PersistDataExpression:
                return $this->Append($QueryBuilder, $Expression->GetPersistExpression());
                
            case $Expression instanceof E\MultipleExpression:
                foreach($Expression->GetExpressions() as $Expression) {
                    $this->Append($QueryBuilder, $Expression);
                }
                return;
                
            case $Expression instanceof CoreE\ColumnExpression:
                return $this->AppendColumn($QueryBuilder, $Expression);
                
            case $Expression instanceof CoreE\ConstantExpression:
                return $this->AppendConstant($QueryBuilder, $Expression);
                
            case $Expression instanceof E\SetExpression:
                return $this->AppendSet($QueryBuilder, $Expression);
                
            case $Expression instanceof E\BinaryOperationExpression:
                return $this->AppendBinaryOperation($QueryBuilder, $Expression);
                
            case $Expression instanceof E\UnaryOperationExpression:
                return $this->AppendUnaryOperation($QueryBuilder, $Expression);
                
            case $Expression instanceof E\CastExpression:
                return $this->AppendCast($QueryBuilder, $Expression);
                
            case $Expression instanceof E\FunctionCallExpression:
                return $this->AppendFunctionCall($QueryBuilder, $Expression);
                
            case $Expression instanceof E\KeywordExpression:
                return $this->AppendKeyword($QueryBuilder, $Expression);
                                
            case $Expression instanceof E\ValueListExpression:
                return $this->AppendList($QueryBuilder, $Expression);

                                
            case $Expression instanceof E\IfExpression:
                return $this->AppendIf($QueryBuilder, $Expression);

            default:
                throw new \Exception();
        }
    }
    
    protected function AppendColumn(QueryBuilder $QueryBuilder, CoreE\ColumnExpression $Expression) {
        $QueryBuilder->AppendColumn('#', $Expression->GetColumn());
    }

    protected function AppendConstant(QueryBuilder $QueryBuilder, CoreE\ConstantExpression $Expression) {
        $QueryBuilder->AppendSingleValue($Expression->GetValue());
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
        $QueryBuilder->Append(' ' . $Expression->GetKeyword() . ' ');
    }

    protected abstract function AppendCast(QueryBuilder $QueryBuilder, E\CastExpression $Expression);

    protected abstract function AppendFunctionCall(QueryBuilder $QueryBuilder, E\FunctionCallExpression $Expression);

    protected abstract function AppendList(QueryBuilder $QueryBuilder, E\ValueListExpression $Expression);

    protected abstract function AppendIf(QueryBuilder $QueryBuilder, E\IfExpression $Expression);

    protected function AppendSet(QueryBuilder $QueryBuilder, E\SetExpression $Expression) {
        $this->Append($QueryBuilder, $Expression->GetLeftOperandExpression());
        $QueryBuilder->Append($this->GetSetOperatorString($Expression->GetOperator()));
        $this->Append($QueryBuilder, $Expression->GetRightOperandExpression());
    }
    protected abstract function GetSetOperatorString($Operator);
}

?>