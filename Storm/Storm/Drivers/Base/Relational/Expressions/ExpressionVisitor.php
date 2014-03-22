<?php

namespace Storm\Drivers\Base\Relational\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionVisitor extends ExpressionWalker {
    
    final public function WalkBoundValue(BoundValueExpression $Expression) {
        $this->VisitBoundValue($Expression);
        return $Expression;
    }
    protected function VisitBoundValue(BoundValueExpression $Expression) {}
    
    public function WalkEscapedValue(EscapedValueExpression $Expression) {
        $this->VisitEscapedValue($Expression);
        return $Expression;
    }
    protected function VisitEscapedValue(EscapedValueExpression $Expression) {}
    
    final public function WalkAlias(AliasExpression $Expression) {
        $this->VisitAlias($Expression);
        return $Expression;
    }
    protected function VisitAlias(AliasExpression $Expression) {}
    
    final public function WalkBinaryOperation(BinaryOperationExpression $Expression) {
        $this->VisitBinaryOperation($Expression);
        return $Expression;
    }
    protected function VisitBinaryOperation(BinaryOperationExpression $Expression) {}
    
    final public function WalkUnaryOperation(UnaryOperationExpression $Expression) {
        $this->VisitUnaryOperation($Expression);
        return $Expression;
    }
    protected function VisitUnaryOperation(UnaryOperationExpression $Expression) {}
    
    final public function WalkFunctionCall(FunctionCallExpression $Expression) {
        $this->VisitFunctionCall($Expression);
        return $Expression;
    }
    protected function VisitFunctionCall(FunctionCallExpression $Expression) {}
    
    final public function WalkValueList(ValueListExpression $Expression) {
        $this->VisitValueList($Expression);
        return $Expression;
    }
    protected function VisitValueList(ValueListExpression $Expression) {}
    
    final public function WalkIf(IfExpression $Expression) {
        $this->VisitIf($Expression);
        return $Expression;
    }
    protected function VisitIf(IfExpression $Expression) {}
    
    final public function WalkIdentifier(IdentifierExpression $Expression) {
        $this->VisitIdentifier($Expression);
        return $Expression;
    }
    protected function VisitIdentifier(IdentifierExpression $Expression) {}
    
    final public function WalkColumn(ColumnExpression $Expression) {
        $this->VisitColumn($Expression);
        return $Expression;
    }
    protected function VisitColumn(ColumnExpression $Expression) {}
    
    final public function WalkLiteral(LiteralExpression $Expression) {
        $this->VisitLiteral($Expression);
        return $Expression;
    }
    protected function VisitLiteral(LiteralExpression $Expression) {}
    
    final public function WalkKeyword(KeywordExpression $Expression) {
        $this->VisitKeyword($Expression);
        return $Expression;
    }
    protected function VisitKeyword(KeywordExpression $Expression) {}
    
    final public function WalkCompundBoolean(CompoundBooleanExpression $Expression) {
        $this->VisitCompundBoolean($Expression);
        return $Expression;
    }
    protected function VisitCompundBoolean(CompoundBooleanExpression $Expression) {}
    
    final public function WalkSubSelect(SubSelectExpression $Expression) {
        $this->VisitSubSelect($Expression);
        return $Expression;
    }
    protected function VisitSubSelect(SubSelectExpression $Expression) {}
    
    final public function WalkMultiple(MultipleExpression $Expression) {
        $this->VisitMultiple($Expression);
        return $Expression;
    }
    protected function VisitMultiple(MultipleExpression $Expression) {}
}

?>