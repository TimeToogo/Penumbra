<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionWalker {
    
    final public static function On($Expression) {
        return $Expression === null ? null : $Expression->Traverse($this);
    }
    
    /**
     * @return Expression|null
     */
    final public function Walk(Expression $Expression = null) {
        return $Expression === null ? null : $Expression->Traverse($this);
    }
    
    /**
     * @return Expression[]
     */
    final public function WalkAll(array $Expressions) {
        $WalkedExpressions = [];
        foreach ($Expressions as $Key => $Expression) {
            $WalkedExpressions[$Key] = $Expression->Traverse($Expression);
        }
        
        return $WalkedExpressions;
    }
    
    public function WalkArray(ArrayExpression $Expression) {
        return $Expression->Update(
                $this->WalkAll($Expression->GetKeyExpressions()), 
                $this->WalkAll($Expression->GetValueExpressions()));
    }
    
    public function WalkAssignment(AssignmentExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetAssignmentValueExpression()), 
                $Expression->GetOperator(),
                $this->Walk($Expression->GetAssignToExpression()));
    }
    
    public function WalkBinaryOperation(BinaryOperationExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetLeftOperandExpression()), 
                $Expression->GetOperator(),
                $this->Walk($Expression->GetRightOperandExpression()));
    }
    
    public function WalkUnaryOperation(UnaryOperationExpression $Expression) {
        return $Expression->Update(
                $Expression->GetOperator(),
                $this->Walk($Expression->GetOperandExpression()));
    }
    
    public function WalkCast(CastExpression $Expression) {
        return $Expression->Update(
                $Expression->GetCastType(),
                $this->Walk($Expression->GetCastValueExpression()));
    }
    
    public function WalkEmpty(EmptyExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()));
    }
    
    public function WalkField(FieldExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()),
                $this->Walk($Expression->GetNameExpression()));
    }
    
    public function WalkMethodCall(MethodCallExpression $Expression) {
        return $Expression->Update(
                $this->WalkAll($Expression->GetArgumentExpressions()),
                $this->Walk($Expression->GetNameExpression()));
    }
    
    public function WalkIndex(IndexExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()),
                $this->Walk($Expression->GetIndexExpression()));
    }
    
    public function WalkInvocation(InvocationExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }
    
    public function WalkFunctionCall(FunctionCallExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetNameExpression()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }
    
    public function WalkEntity(EntityExpression $Expression) {
        return $Expression;
    }
    
    public function WalkNew(NewExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetClassType()),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }
    
    public function WalkProperty(PropertyExpression $Expression) {
        return $Expression->Update(
                $Expression->GetProperty(),
                $this->Walk($Expression->GetParentPropertyExpression()));
    }
    
    public function WalkReturn(ReturnExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()));
    }
    
    public function WalkTernary(TernaryExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetConditionExpression()),
                $this->Walk($Expression->GetIfTrueExpression()),
                $this->Walk($Expression->GetIfFalseExpression()));
    }
    
    public function WalkUnresolvedValue(UnresolvedVariableExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetNameExpression()));
    }
    
    public function WalkValue(ValueExpression $Expression) {
        return $Expression;
    }
    
    public function WalkAggregate(Aggregates\AggregateExpression $Expression) {
        return $Expression;
    }
    
    public function WalkClosure(ClosureExpression $Expression) {
        return $Expression->Update(
                $Expression->GetParameterNames(), 
                $Expression->GetUsedVariableNames(), 
                $this->WalkAll($Expression->GetBodyExpressions()));
    }
}

?>