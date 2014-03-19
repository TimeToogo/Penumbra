<?php

namespace Storm\Drivers\Base\Relational\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionWalker {
    
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
            $WalkedExpressions[$Key] = $this->Walk($Expression);
        }
        
        return $WalkedExpressions;
    }
    
    public function WalkBoundValue(BoundValueExpression $Expression) {
        return $Expression;
    }
    
    public function WalkEscapedValue(EscapedValueExpression $Expression) {
        return $Expression;
    }
    
    public function WalkAlias(AliasExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetValueExpression()), 
                $Expression->GetAlias());
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
    
    public function WalkFunctionCall(FunctionCallExpression $Expression) {
        return $Expression->Update(
                $Expression->GetName(),
                $this->WalkAll($Expression->GetArgumentExpressions()));
    }
    
    public function WalkValueList(ValueListExpression $Expression) {
        return $Expression->Update(
                $this->WalkAll($Expression->GetValueExpressions()));
    }
    
    public function WalkIf(IfExpression $Expression) {
        return $Expression->Update(
                $this->Walk($Expression->GetConditionExpression()),
                $this->Walk($Expression->GetIfTrueExpression()),
                $this->Walk($Expression->GetIfFalseExpression()));
    }
    
    public function WalkIdentifier(IdentifierExpression $Expression) {
        return $Expression;
    }
    
    public function WalkColumn(ColumnExpression $Expression) {
        return $Expression;
    }
    
    public function WalkLiteral(LiteralExpression $Expression) {
        return $Expression;
    }
    
    public function WalkKeyword(KeywordExpression $Expression) {
        return $Expression;
    }
    
    public function WalkCompundBoolean(CompoundBooleanExpression $Expression) {
        return $Expression->Update(
                $this->WalkAll($Expression->GetBooleanExpressions()), 
                $Expression->GetLogicalOperator());
    }
    
    public function WalkSubSelect(SubSelectExpression $Expression) {
        return $Expression;
    }
    
    public function WalkMultiple(MultipleExpression $Expression) {
        return $Expression->Update(
                $this->WalkAll($Expression->GetExpressions()));
    }
}

?>