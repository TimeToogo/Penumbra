<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Expressions as E;

abstract class ExpressionCompiler extends Queries\ExpressionCompiler {
    public function __construct(Queries\IExpressionOptimizer $ExpressionOptimizer) {
        parent::__construct($ExpressionOptimizer);
        $this->BinaryOperators = $this->BinaryOperators();
        $this->SetOperators = $this->SetOperators();
        $this->UnaryOperators = $this->UnaryOperators();
        $this->CastTypes = $this->CastTypes();
    }
    
    protected function AppendBinaryOperation(QueryBuilder $QueryBuilder, E\BinaryOperationExpression $Expression) {
        $QueryBuilder->Append('(');
        parent::AppendBinaryOperation($QueryBuilder, $Expression);
        $QueryBuilder->Append(')');
    }

    protected $BinaryOperators;
    protected abstract function BinaryOperators();

    protected function GetBinaryOperatorString($Operator) {
        if (isset($this->BinaryOperators[$Operator])) {
            return ' ' . $this->BinaryOperators[$Operator] . ' ';
        }
        else {
            throw new \Exception;
        }
    }

    protected $SetOperators;
    protected abstract function SetOperators();
    
    protected function GetSetOperatorString($Operator) {
        if (isset($this->SetOperators[$Operator])) {
            return ' ' . $this->SetOperators[$Operator] . ' ';
        }
        else {
            throw new \Exception;
        }
    }
    
    protected function AppendUnaryOperation(QueryBuilder $QueryBuilder, \Storm\Drivers\Base\Relational\Expressions\UnaryOperationExpression $Expression) {
        $QueryBuilder->Append('(');
        parent::AppendUnaryOperation($QueryBuilder, $Expression);
        $QueryBuilder->Append(')');
    }

    protected $UnaryOperators;
    protected abstract function UnaryOperators();

    protected function GetUnaryOperatorString($Operator) {
        if (isset($this->UnaryOperators[$Operator])) {
            return $this->UnaryOperators[$Operator];
        } 
        else {
            throw new \Exception();
        }
    }
    
    protected $CastTypes;
    protected abstract function CastTypes();
    
    protected function GetCastTypeString($Operator) {
        if (isset($this->CastTypes[$Operator])) {
            return $this->CastTypes[$Operator];
        } 
        else {
            throw new \Exception();
        }
    }
    
    protected function AppendCast(QueryBuilder $QueryBuilder, E\CastExpression $Expression) {
        $QueryBuilder->Append('CAST');
        $QueryBuilder->Append('(');
        $this->Append($QueryBuilder, $Expression->GetCastValueExpression());
        $QueryBuilder->Append(' AS ' . $this->GetCastTypeString($Expression->GetCastType()));
        $QueryBuilder->Append(')');
    }
    
    protected function AppendFunctionCall(QueryBuilder $QueryBuilder, E\FunctionCallExpression $Expression) {
        $QueryBuilder->Append($Expression->GetName());
        
        $QueryBuilder->Append('(');
        $First = true;
        foreach ($Expression->GetArgumentValueListExpression()->GetValueExpressions() as $ArgumentExpression) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $this->Append($QueryBuilder, $ArgumentExpression);
        }
        $QueryBuilder->Append(')');
    }

    protected function AppendCompoundBoolean(QueryBuilder $QueryBuilder, E\CompoundBooleanExpression $Expression) {
        $QueryBuilder->Append('(');
        parent::AppendCompoundBoolean($QueryBuilder, $Expression);
        $QueryBuilder->Append(')');
    }
    
    protected function AppendList(QueryBuilder $QueryBuilder, E\ValueListExpression $Expression) {
        $QueryBuilder->Append('(');
        $First = true;
        foreach ($Expression->GetValueExpressions() as $ValueExpression) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $this->Append($QueryBuilder, $ValueExpression);
        }
        $QueryBuilder->Append(')');
    }

    protected function AppendIf(QueryBuilder $QueryBuilder, E\IfExpression $Expression) {
        $QueryBuilder->Append('IF');
        $QueryBuilder->Append('(');
        $this->Append($QueryBuilder, $Expression->GetConditionExpression());
        $QueryBuilder->Append(',');
        $this->Append($QueryBuilder, $Expression->GetIfTrueExpression());
        $QueryBuilder->Append(',');
        $this->Append($QueryBuilder, $Expression->GetIfFalseExpression());
        $QueryBuilder->Append(')');
    }
}

?>