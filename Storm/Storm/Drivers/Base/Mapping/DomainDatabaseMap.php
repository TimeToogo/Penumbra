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
        /* @var $ExpressionMapper RR\IExpressionMapper */
        $ExpressionMapper = $this->GetDatabase()->GetPlatform()->GetExpressionMapper();
        switch (true) {
            case $Expression instanceof O\PropertyExpression:
                return array_map(
                    [Expression::GetType(), 'ReviveColumn'], 
                    $EntityRelationalMap->GetMappedReviveColumns($Expression->GetProperty()));
            
            case $Expression instanceof O\ConstantExpression:
                return [$ExpressionMapper->MapConstantExpression($Expression->GetValue())];
                        
            case $Expression instanceof O\ObjectExpression:
                if(!$Expression->HasInstance()) {
                    throw new \Exception();
                }
                return [$ExpressionMapper->MapObjectExpression(
                        $Expression->GetClassType(), 
                        $Expression->GetInstance())];
            
            case $Expression instanceof O\MethodCallExpression:
                $ObjectExpression = $Expression->GetObjectExpression();
                return [$ExpressionMapper->MapMethodCallExpression(
                        $Expression->IsStatic() ? 
                                null : $this->MapExpression($EntityRelationalMap, $ObjectExpression)[0],
                        $Expression->GetClassType(),
                        $Expression->GetName(),
                        array_map(function($Expression) use (&$EntityRelationalMap) {
                            return $this->MapExpression($EntityRelationalMap, $Expression)[0];
                        }, $Expression->GetArgumentValueExpressions()))];
            
            case $Expression instanceof O\ArrayExpression:
                return [Expression::ValueList(array_map(
                        function($Expression) use (&$EntityRelationalMap) {
                            return $this->MapExpression($EntityRelationalMap, $Expression)[0];
                        }, $Expression->GetValueExpressions()))];
            
            //Limitation: only one to one mapping for some expressions 
                        
            case $Expression instanceof O\AssignmentExpression:
                $ColumnExpressions = array_map([Expression::GetType(), 'Column'], 
                        $EntityRelationalMap->GetMappedPersistColumns($Expression->GetProperty()));
                $Operator = $this->OperatorMapper->MapAssignmentOperator($Expression->GetOperator());
                $SetValueExpression = $this->MapExpression($EntityRelationalMap, $Expression->GetRightOperandExpression())[0];
                return array_map(
                        function ($ColumnExpression) use (&$Operator, &$SetValueExpression) {
                            return Expression::Set(
                                    $ColumnExpression, 
                                    $Operator, 
                                    Expression::PersistData(
                                            $ColumnExpression->GetColumn(), $SetValueExpression)
                                    );
                        }, $ColumnExpressions); 
                        
            case $Expression instanceof O\BinaryOperationExpression:
                return [$ExpressionMapper->MapBinaryOperationExpression(
                        $this->MapExpression($EntityRelationalMap, $Expression->GetLeftOperandExpression())[0], 
                        $this->OperatorMapper->MapBinaryOperator($Expression->GetOperator()), 
                        $this->MapExpression($EntityRelationalMap, $Expression->GetRightOperandExpression())[0])];
            
            case $Expression instanceof O\UnaryOperationExpression:
                return [$ExpressionMapper->MapUnaryOperationExpression(
                        $this->OperatorMapper->MapUnaryOperator($Expression->GetOperator()), 
                        $this->MapExpression($EntityRelationalMap, $Expression->GetOperandExpression())[0])];
            
            case $Expression instanceof O\CastExpression:
                return [$ExpressionMapper->MapCastExpression(
                        $this->OperatorMapper->MapCastOperator($Expression->GetCastType()),
                        $this->MapExpression($EntityRelationalMap, $Expression->GetCastValueExpression())[0])];
            
            case $Expression instanceof O\FunctionCallExpression:
                return [$ExpressionMapper->MapFunctionCallExpression(
                        $Expression->GetName(),
                        array_map(function($Expression) use (&$EntityRelationalMap) {
                            return $this->MapExpression($EntityRelationalMap, $Expression)[0];
                        }, $Expression->GetArgumentValueExpressions()))];
            
            default:
                throw new \Exception();
        }
    }
    
}

?>