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
    
    protected function AppendSubSelect(QueryBuilder $QueryBuilder, E\SubSelectExpression $Expression) {
        $QueryBuilder->Append('(');
        $QueryBuilder->AppendSelect($Expression->GetSelect());
        $QueryBuilder->Append(')');
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
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied binary operator: %s', 
                    get_class($this),
                    $Operator);
        }
    }

    protected $SetOperators;
    protected abstract function SetOperators();
    
    protected function GetSetOperatorString($Operator) {
        if (isset($this->SetOperators[$Operator])) {
            return ' ' . $this->SetOperators[$Operator] . ' ';
        }
        else {
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied set operator: %s', 
                    get_class($this),
                    $Operator);
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
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied unary operator: %s', 
                    get_class($this),
                    $Operator);
        }
    }
    
    protected $CastTypes;
    protected abstract function CastTypes();
    
    protected function GetCastTypeString($Operator) {
        if (isset($this->CastTypes[$Operator])) {
            return $this->CastTypes[$Operator];
        } 
        else {
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied cast type: %s', 
                    get_class($this),
                    $Operator);
        }
    }
    
    protected function AppendCast(QueryBuilder $QueryBuilder, E\CastExpression $Expression) {
        $QueryBuilder->Append('CAST');
        $QueryBuilder->Append('(');
        $this->Append($QueryBuilder, $Expression->GetCastValueExpression());
        $QueryBuilder->Append(' ' . $this->GetCastAsKeyword() . ' ');
        $QueryBuilder->Append($this->GetCastTypeString($Expression->GetCastType()));
        $QueryBuilder->Append(')');
    }
    protected function GetCastAsKeyword() {
        return 'AS';
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
        $QueryBuilder->Append('CASE ');
        
        $this->AppendBinaryOperation($QueryBuilder, 
                E\Expression::BinaryOperation(
                        $Expression->GetConditionExpression(), 
                        E\Operators\Binary::LogicalAnd, 
                        E\Expression::Constant(1)));
        
        $QueryBuilder->Append('WHEN 1 THEN');
        $this->Append($QueryBuilder, $Expression->GetIfTrueExpression());
        
        $QueryBuilder->Append('ELSE');
        $this->Append($QueryBuilder, $Expression->GetIfFalseExpression());
        
        $QueryBuilder->Append('END');
    }
}

?>