<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Drivers\Base\Relational\Expressions\Operators;

final class ExpressionCompiler extends Queries\ExpressionCompiler {
    // <editor-fold defaultstate="collapsed" desc="Binary Operations">
    protected function AppendBinaryOperation(QueryBuilder $QueryBuilder, E\BinaryOperationExpression $Expression) {
        $QueryBuilder->Append('(');
        parent::AppendBinaryOperation($QueryBuilder, $Expression);
        $QueryBuilder->Append(')');
    }

    private static $BinaryOperators = [
        Operators\Binary::Addition => '+',
        Operators\Binary::BitwiseAnd => '&',
        Operators\Binary::BitwiseOr => '|',
        Operators\Binary::BitwiseXor => '^',
        Operators\Binary::Division => '/',
        Operators\Binary::Equality => '=',
        Operators\Binary::GreaterThan => '>',
        Operators\Binary::GreaterThanOrEqualTo => '>=',
        Operators\Binary::In => 'IN',
        Operators\Binary::Inequality => '!=',
        Operators\Binary::LessThan => '<',
        Operators\Binary::LessThanOrEqualTo => '<=',
        Operators\Binary::LogicalAnd => 'AND',
        Operators\Binary::LogicalOr => 'OR',
        Operators\Binary::MatchesRegularExpression => 'REGEXP',
        Operators\Binary::Modulus => '%',
        Operators\Binary::Multiplication => '*',
        Operators\Binary::ShiftLeft => '<<',
        Operators\Binary::ShiftRight => '>>',
        Operators\Binary::Subtraction => '-',
    ];

    protected function GetBinaryOperatorString($Operator) {
        if (!isset(static::$BinaryOperators[$Operator])) {
            throw new Exception;
        }         
        else {
            return ' ' . static::$BinaryOperators[$Operator] . ' ';
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Set Operations">
    protected function AppendSet(QueryBuilder $QueryBuilder, E\SetExpression $Expression) {
        $QueryBuilder->Append('SET ');
        parent::AppendSet($QueryBuilder, $Expression);
    }

    protected function GetSetOperatorString($Operator) {
        if ($Operator !== Operators\Assignment::Equal) {
            throw new Exception('Mysql only support equal');
        }
        return ' = ';
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Unary Operations">
    protected function AppendUnaryOperation(QueryBuilder $QueryBuilder, \Storm\Drivers\Base\Relational\Expressions\UnaryOperationExpression $Expression) {
        $QueryBuilder->Append('(');
        parent::AppendUnaryOperation($QueryBuilder, $Expression);
        $QueryBuilder->Append(')');
    }

    private static $UnaryOperators = [
        Operators\Unary::BitwiseNot => '~',
        Operators\Unary::Negation => '-',
        Operators\Unary::Not => '!',
    ];

    protected function GetUnaryOperatorString($Operator) {
        if (!isset(static::$UnaryOperators[$Operator])) {
            throw new Exception;
        } 
        else {
            return ' ' . static::$UnaryOperators[$Operator] . ' ';
        }
    }

    // </editor-fold>

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

    protected function AppendList(QueryBuilder $QueryBuilder, E\ValueListExpression $Expression) {
        $QueryBuilder->Append('(');
        $First = true;
        foreach ($Expression->GetArgumentValueExpressions() as $ValueExpression) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $this->Append($QueryBuilder, $ValueExpression);
        }
        $QueryBuilder->Append(')');
    }
    
    protected function AppendCast(QueryBuilder $QueryBuilder, E\CastExpression $Expression) {
        $QueryBuilder->Append('CAST');
        $QueryBuilder->Append('(');
        $this->Append($QueryBuilder, $Expression->GetCastValueExpression());
        $QueryBuilder->Append(' AS ' . $Expression->GetCastType());
        $QueryBuilder->Append(')');
    }

}

?>