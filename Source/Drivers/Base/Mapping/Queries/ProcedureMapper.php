<?php

namespace Penumbra\Drivers\Base\Mapping\Queries;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Mapping\ExpressionMapper;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Core\Object\Expressions\Operators;

class ProcedureMapper extends CriteriaMapper {
    
    public function MapProcedure(
            Object\IProcedure $Procedure,
            Relational\Update $Update,
            ExpressionMapper $ExpressionMapper) {
        
        $this->MapCriteria($Procedure->GetCriteria(), $Update->GetCriteria(), $ExpressionMapper);
        
        $AssignmentExpressions = $Procedure->GetExpressions();
        
        foreach($AssignmentExpressions as $AssignmentExpression) {
            $AssignToExpression = $AssignmentExpression->GetAssignToExpression();
            
            if(!($AssignToExpression instanceof Object\Expressions\PropertyExpression)) {
                throw new Mapping\MappingException('Cannot map assignment expression: invalid assign to expression expecting %s, %s given',
                        Object\Expressions\PropertyExpression::GetType(),
                        $AssignToExpression->GetType());
            }
            
            $ColumnExpression = $ExpressionMapper->Map($AssignToExpression);
            $Column = $ColumnExpression->GetColumn();
            
            $AssignmentValueExpression = $this->MapAssignmentToBinaryExpression($AssignmentExpression);
            $MappedAssignmentValueExpression = $ExpressionMapper->Map($AssignmentValueExpression);
            
            $Update->AddColumn($Column, $Column->GetPersistExpression($MappedAssignmentValueExpression));
        }
        
        return $Update;
    }
    
    private static $AssignmentToBinaryOperatorMap = [
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
    private function MapAssignmentToBinaryExpression(O\AssignmentExpression $Expression) {
        $AssignmentOperator = $Expression->GetOperator();
        if(isset(self::$AssignmentToBinaryOperatorMap[$AssignmentOperator])) {
            return O\Expression::BinaryOperation(
                    $Expression->GetAssignToExpression(), 
                    self::$AssignmentToBinaryOperatorMap[$AssignmentOperator], 
                    $Expression->GetAssignmentValueExpression());
        }
        else {
            return $Expression->GetAssignmentValueExpression();
        }
    }
}

?>