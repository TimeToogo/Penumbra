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
        switch (true) {
            case $Expression instanceof O\PropertyExpression://TODO
                return Expression::ReviveColumn($EntityRelationalMap->GetMappedColumns($Expression->GetProperty()));
            
            case $Expression instanceof O\ConstantExpression:
                return $ExpressionMapper->MapConstantExpression($Expression->GetValue());
            
            case $Expression instanceof O\ArrayExpression:
                return Expression::ValueList(array_map(
                        function($Expression) use (&$EntityRelationalMap) {
                            return $this->MapExpression($EntityRelationalMap, $Expression);
                        }, $Expression->GetValueExpressions()));
            
            case $Expression instanceof O\AssignmentExpression:
                return $ExpressionMapper->MapAssignmentExpression(
                        $EntityRelationalMap->GetMappedColumns($Expression->GetProperty()), 
                        $this->OperatorMapper->MapAssignmentOperator($ExpressionMapper->GetOperator()), 
                        $this->MapExpression($EntityRelationalMap, $ExpressionMapper->GetRightOperandExpression()));
            
            case $Expression instanceof O\BinaryOperationExpression:
                return $ExpressionMapper->MapBinaryOperationExpression(
                        $this->MapExpression($EntityRelationalMap, $Expression->GetLeftOperandExpression()), 
                        $this->OperatorMapper->MapBinaryOperator($Expression->GetOperator()), 
                        $this->MapExpression($EntityRelationalMap, $Expression->GetRightOperandExpression()));
            
            case $Expression instanceof O\UnaryOperationExpression:
                return $ExpressionMapper->MapUnaryOperationExpression(
                        $this->OperatorMapper->MapUnaryOperator($Expression->GetExpression()), 
                        $this->MapExpression($EntityRelationalMap, $Expression->GetOperandExpression()));
            
            case $Expression instanceof O\CastExpression:
                return $ExpressionMapper->MapCastExpression(
                        $this->OperatorMapper->MapCastOperator($Expression->GetCastType()),
                        $this->MapExpression($EntityRelationalMap, $Expression->GetCastValueExpression()));
            
            case $Expression instanceof O\FunctionCallExpression:
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