<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expressions as R;
use \Storm\Drivers\Base\Relational\Expressions as RR;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $MappingConfiguration;
    private $OperatorMapper;
    
    public function __construct() {
        $this->MappingConfiguration = $this->MappingConfiguration();
        $this->OperatorMapper = new OperatorMapper();
        
        parent::__construct();
    }
    protected abstract function MappingConfiguration();
    
    /**
     * @return IMappingConfiguration
     */
    final public function GetMappingConfiguration() {
        return $this->MappingConfiguration;
    }
    
    final protected function MapExpression(Mapping\IEntityRelationalMap $EntityRelationalMap, 
            O\Expression $Expression) {
        $ExpressionMapper = $this->GetDatabase()->GetPlatform()->GetExpressionMapper();
        switch ($Expression->GetType()) {
            case O\PropertyExpression::GetType():
                return Expression::ReviveColumn($EntityRelationalMap->GetMappedColumn($Expression->GetProperty()));
            
            case O\ConstantExpression::GetType():
                return $ExpressionMapper->MapConstantExpression($Expression->GetValue());
            
            case O\ArrayExpression::GetType():
                return Expression::ValueList(array_map(
                        function($Expression) use (&$EntityRelationalMap) {
                            return $this->MapExpression($EntityRelationalMap, $Expression);
                        }, $Expression->GetValueExpressions()));
            
            case O\AssignmentExpression::GetType():
                return $ExpressionMapper->MapAssignmentExpression(
                        $EntityRelationalMap->GetMappedColumn($Expression->GetProperty()), 
                        $this->OperatorMapper->MapAssignmentOperator($ExpressionMapper->GetOperator()), 
                        $this->MapExpression($EntityRelationalMap, $ExpressionMapper->GetRightOperandExpression()));
            
            case O\BinaryOperationExpression::GetType():
                return $ExpressionMapper->MapBinaryOperationExpression(
                        $this->MapExpression($EntityRelationalMap, $Expression->GetLeftOperandExpression()), 
                        $this->OperatorMapper->MapBinaryOperator($Expression->GetOperator()), 
                        $this->MapExpression($EntityRelationalMap, $Expression->GetRightOperandExpression()));
            
            case O\UnaryOperationExpression::GetType():
                return $ExpressionMapper->MapUnaryOperationExpression(
                        $this->OperatorMapper->MapUnaryOperator($Expression->GetExpression()), 
                        $this->MapExpression($EntityRelationalMap, $Expression->GetOperandExpression()));
            
            case O\CastExpression::GetType():
                return $ExpressionMapper->MapCastExpression(
                        $this->OperatorMapper->MapCastOperator($Expression->GetCastType()),
                        $this->MapExpression($EntityRelationalMap, $Expression->GetCastValueExpression()));
            
            case O\FunctionCallExpression::GetType():
                return $ExpressionMapper->MapFunctionCallExpression(
                        $Expression->GetName(),
                        array_map(function($Expression) use (&$EntityRelationalMap) {
                            return $this->MapExpression($EntityRelationalMap, $Expression);
                        }, $Expression->GetArgumentValueExpressions()));
            
            default:
                throw new Exception();
        }
    }
    
}

?>