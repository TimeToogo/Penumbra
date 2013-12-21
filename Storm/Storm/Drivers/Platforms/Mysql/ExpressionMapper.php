<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions\IExpressionMapper;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Core\Relational\Expressions as EE;
use \Storm\Drivers\Base\Relational\Expressions\Operators as O;

final class ExpressionMapper implements IExpressionMapper {
    
    public function MapConstantExpression($Value) {
        if($Value instanceof \DateTime) {
            return Expression::FunctionCall('FROM_UNIXTIME', [new EE\ConstantExpression($Value->getTimestamp())]);
        }
        
        return Expression::Constant($Value);
    }
    
    private static $AssignmentBinaryOperatorMap = [
        O\Assignment::Addition => O\Binary::Addition,
        O\Assignment::BitwiseAnd => O\Binary::BitwiseAnd,
        O\Assignment::BitwiseOr => O\Binary::BitwiseOr,
        O\Assignment::BitwiseXor => O\Binary::BitwiseXor,
        O\Assignment::Concatenate => O\Binary::Concatenation,
        O\Assignment::Division => O\Binary::Division,
        O\Assignment::Modulus => O\Binary::Modulus,
        O\Assignment::Multiplication => O\Binary::Multiplication,
        O\Assignment::ShiftLeft => O\Binary::ShiftLeft,
        O\Assignment::ShiftRight => O\Binary::ShiftRight,
        O\Assignment::Subtraction => O\Binary::Subtraction,
    ];
    public function MapAssignmentExpression(
            Relational\IColumn $Column, 
            $AssignmentOperator, 
            CoreExpression $ValueExpression) {
        
        if(isset(static::$AssignmentBinaryOperatorMap[$AssignmentOperator])) {
            return Expression::Set(
                    $Column, 
                    O\Assignment::Equal, 
                    Expression::BinaryOperation(
                            Expression::Column($Column), 
                            static::$AssignmentBinaryOperatorMap[$AssignmentOperator], 
                            $ValueExpression));
        }
        else {
            return Expression::Set(
                    $Column, 
                    O\Assignment::Equal, 
                    $ValueExpression);
        }
    }
    
    public function MapBinaryOperationExpression(
            CoreExpression $LeftOperandExpression, 
            $BinaryOperator, 
            CoreExpression $RightOperandExpression) {
        if($LeftOperandExpression instanceof E\FunctionCallExpression) {
            if($LeftOperandExpression->GetName() === 'INSTR') {
                if($RightOperandExpression instanceof EE\ConstantExpression) {
                    if($RightOperandExpression->GetValue() === false) {
                        $RightOperandExpression = new EE\ConstantExpression(-1);
                    }
                }
            }
        }
        
        switch($BinaryOperator) {
            case O\Binary::Concatenation:
                return new E\FunctionCallExpression('CONCAT', [$LeftOperandExpression, $RightOperandExpression]);
            
            default:
                return new E\BinaryOperationExpression(
                        $LeftOperandExpression, 
                        $BinaryOperator, 
                        $RightOperandExpression);
        }
    }
    
    public function MapUnaryOperationExpression($UnaryOperator, CoreExpression $OperandExpression) {
        switch ($UnaryOperator) {
            case O\Unary::Increment:
                return new E\BinaryOperationExpression(
                        $OperandExpression, 
                        O\Binary::Addition, 
                        new EE\ConstantExpression(1));
                
            case O\Unary::Decrement:
                return new E\BinaryOperationExpression(
                        $OperandExpression, 
                        O\Binary::Subtraction, 
                        new EE\ConstantExpression(1));
            
            case O\Unary::PreIncrement:
                return new E\BinaryOperationExpression(
                        new EE\ConstantExpression(1), 
                        O\Binary::Subtraction, 
                        $OperandExpression);
            
            case O\Unary::PreDecrement:
                return new E\BinaryOperationExpression(
                        new EE\ConstantExpression(1), 
                        O\Binary::Subtraction, 
                        $OperandExpression);

            default:
                return Expression::UnaryOperation($UnaryOperator, $OperandExpression);
        }
    }
    
    public function MapCastExpression($CastType, CoreExpression $CastValueExpression) {
        switch ($CastType) {
            case O\Cast::Boolean:
                return Expression::FunctionCall('IF', 
                        [$CastValueExpression, 
                        Expression::Constant(1), 
                        Expression::Constant(0)]);
            
            case O\Cast::Double:
                return Expression::BinaryOperation(
                        $CastValueExpression, 
                        O\Binary::Addition, 
                        '0.0D');
                
            case O\Cast::Integer:
                return Expression::Cast('INTEGER', $CastValueExpression);
            
            case O\Cast::String:
                return Expression::Cast('CHAR', $CastValueExpression);
            
            default:
                throw new Exception();
        }
    }

    public function MapFunctionCallExpression($FunctionName, array $ArgumentExpressions) {
        $MysqlFunctionName = null;
        $MysqlArgumentExpressions = $ArgumentExpressions;
        $Name = strtolower($FunctionName);
        switch ($Name) {
            case 'strtoupper':
                $MysqlFunctionName = 'UPPER';
                break;
            case 'strtolower':
                $MysqlFunctionName = 'LOWER';
                break;
            
            case 'strpos':
            case 'stripos':
                $MysqlFunctionName = 'INSTR';
                $StringExpression = $ArgumentExpressions[1];
                if(isset($ArgumentExpressions[2])) {
                    $StringExpression = new E\FunctionCallExpression(
                            'SUBSTR', [$ArgumentExpressions[1], $ArgumentExpressions[2]]);
                    unset($MysqlArgumentExpressions[2]);
                }
                if($Name === 'strpos') {
                    $StringExpression = new E\FunctionCallExpression('BINARY', [$StringExpression]);
                }
                $ArgumentExpressions[1] = $StringExpression;
                
                return new E\BinaryOperationExpression(
                        new E\FunctionCallExpression($MysqlFunctionName, $MysqlArgumentExpressions),
                        O\Binary::Subtraction, 
                        new EE\ConstantExpression(1));
                
            case 'bin2hex':
                $MysqlFunctionName = 'HEX';
                break;
            case 'hex2bin':
                $MysqlFunctionName = 'UNHEX';
                break;
            
            case 'trim':
            case 'rtrim':
            case 'ltrim':
                $MysqlFunctionName = 'TRIM';
                
                $DirectionKeyword = null;
                if($Name === 'trim')
                    $DirectionKeyword = 'BOTH';
                else if($Name === 'rtrim')
                    $DirectionKeyword = 'LEADING';
                else
                    $DirectionKeyword = 'TRAILING';
                
                $MysqlArgumentExpressions = [
                    new E\KeywordExpression($DirectionKeyword),
                    isset($ArgumentExpressions[1]) ?
                            $ArgumentExpressions[1] : new EE\ConstantExpression(" \t\n\r\0\x0B"),
                    new E\KeywordExpression('FROM'),
                    new $ArgumentExpressions[0],
                ];
                break;
            
            case 'in_array':
                return new E\BinaryOperationExpression($ArgumentExpressions[0], O\Binary::In, $ArgumentExpressions[1]);
            
            case 'time':
                $MysqlFunctionName = 'UNIX_TIMESTAMP';
                break;
            
            case 'strlen':
                $MysqlFunctionName = 'CHAR_LENGTH';
                break;
            
            case 'preg_match':
                return new E\BinaryOperationExpression(
                        $ArgumentExpressions[1], 
                        O\Binary::MatchesRegularExpression, 
                        new E\FunctionCallExpression('BINARY', [$ArgumentExpressions[0]]));            
            
            case 'strrev':
                $MysqlFunctionName = 'REVERSE';
                break;
            
            case 'substr':
                $MysqlFunctionName = 'SUBSTRING';
                break;
            
            case 'md5':
            case 'sha1':
                $MysqlFunctionName = $Name === 'md5' ? 'MD5' : 'SHA1';
                if(isset($ArgumentExpressions[1])) {
                    unset($MysqlArgumentExpressions[1]);
                    if($ArgumentExpressions[1]) {
                        return 
                        new E\FunctionCallExpression('UNHEX', [
                            new E\FunctionCallExpression
                                    ($MysqlFunctionName, $MysqlArgumentExpressions)]);
                    }
                }
                break;
            
            case 'pow':
                $MysqlFunctionName = 'POWER';
                break;
            
            case 'pi':
                $MysqlFunctionName = 'PI';
                break;
            
            case 'round':
                $MysqlFunctionName = 'ROUND';
                if(isset($ArgumentExpressions[2]))
                    throw new Exception('Does not suppor rounding modes');
                break;
            
            case 'rand':
            case 'mt_rand':
                $IsMt = $Name === 'mt_rand';
                $Minimum = null;
                $Maximum = null;
                if(count($ArgumentExpressions === 0)) {
                    $Minimum = new EE\ConstantExpression(0);
                    $Maximum = new EE\ConstantExpression($IsMt ? mt_getrandmax() : getrandmax());
                }
                else {
                    $Minimum = $ArgumentExpressions[0];
                    $Maximum = $ArgumentExpressions[1];
                }
                $DifferenceExpression = new E\BinaryOperationExpression(
                        $Maximum, 
                        O\Binary::Subtraction, 
                        $Minimum);
                
                return new E\FunctionCallExpression('ROUND', 
                        new E\BinaryOperationExpression(
                                $Minimum, 
                                O\Binary::Addition, 
                                new E\BinaryOperationExpression(
                                        new E\FunctionCallExpression('RAND'), 
                                        O\Binary::Multiplication, 
                                        $DifferenceExpression)));
            
            case 'ceil':
                $MysqlFunctionName = 'CEILING';
                break;
            case 'floor':
                $MysqlFunctionName = 'FLOOR';
                break;
            case 'sqrt':
                $MysqlFunctionName = 'SQRT';
                break;
            
            default:
                throw new Exception();
        }
        
        return new E\FunctionCallExpression($MysqlFunctionName, $MysqlArgumentExpressions);
    }
}

?>