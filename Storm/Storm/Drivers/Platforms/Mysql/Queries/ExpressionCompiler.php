<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Core\Relational\Expressions as EE;
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
        Operators\Binary::Equality => '<=>',
        Operators\Binary::GreaterThan => '>',
        Operators\Binary::GreaterThanOrEqualTo => '>=',
        Operators\Binary::In => 'IN',
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
        if (isset(self::$BinaryOperators[$Operator])) {
            return ' ' . self::$BinaryOperators[$Operator] . ' ';
        }
        else {
            throw new \Exception;
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Set Operations">
    
    protected function GetSetOperatorString($Operator) {
        if ($Operator !== Operators\Assignment::Equal) {
            throw new \Exception('Mysql only supports assignment equal operator');
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
        if (isset(self::$UnaryOperators[$Operator])) {
            return self::$UnaryOperators[$Operator];
        } 
        else {
            throw new \Exception();
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
        foreach ($Expression->GetValueExpressions() as $ValueExpression) {
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