<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

abstract class ValueExpression extends Expression {
    // <editor-fold defaultstate="collapsed" desc="Control Flow">
    /**
     * @return ReturnExpression
     */
    final public function ReturnValue() {
        return new ReturnExpression($this);
    }


    /**
     * @return BreakExpression
     */
    final public function BreakValue() {
        return new BreakExpression($this);
    }


    /**
     * @return ContinueExpression
     */
    final public function ContinueValue() {
        return new ContinueExpression($this);
    }


    /**
     * @return EchoExpression
     */
    final public function EchoValue() {
        return new EchoExpression($this);
    }
    
    /**
     * @return ThrowExpression
     */
    final public function ThrowValue() {
        return new ThrowExpression($this);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Unary Operations">
    /**
     * @return UnaryOperationExpression
     */
    final public function Operate($UnaryOperator) {
        return new UnaryOperationExpression($UnaryOperator, $this);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    final public function Negate() {
        return $this->Operate(Operators\Unary::Negation);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    final public function Increment() {
        return $this->Operate(Operators\Unary::Increment);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    final public function Decrement() {
        return $this->Operate(Operators\Unary::Increment);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    final public function PreIncrement() {
        return $this->Operate(Operators\Unary::PreDecrement);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    final public function PreDecrement() {
        return $this->Operate(Operators\Unary::PreDecrement);
    }

    /**
     * @return UnaryOperationExpression
     */
    final public function Not() {
        return $this->Operate(Operators\Unary::Not);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    final public function ShutUp() {
        return $this->Operate(Operators\Unary::ShutUp);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Binary Operations">
    /**
     * @return BinaryOperationExpression
     */
    final public function OperateWith($BinaryOperator, ValueExpression $OtherOperand) {
        return new BinaryOperationExpression($this, $BinaryOperator, $OtherOperand);
    }

    /**
     * @return BinaryOperationExpression
     */
    final public function AndAlso(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::LogicalAnd, $OtherOperand);
    }

    /**
     * @return BinaryOperationExpression
     */
    final public function OrElse(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::LogicalOr, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function EqualTo(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Equality, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function IdenticalTo(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Identity, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function NotEqualTo(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Inequality, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function NotIdenticalTo(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::NonIdentity, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function Add(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Addition, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function Subtract(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Subtraction, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function Multiply(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Multiplication, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function Divide(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Division, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function Modulus(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Modulus, $OtherOperand);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public function Concatenate(ValueExpression $OtherOperand) {
        return $this->OperateWith(Operators\Binary::Concatenation, $OtherOperand);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Assignment Operations">
    /**
     * @return AssignmentExpression
     */
    final public function AssignFrom($AssignmentOperator, ValueExpression $AssignmentValueExpression) {
        return new AssignmentExpression($this, $AssignmentOperator, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function AssignAs(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::Equal, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function AssignAsReference(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::EqualReference, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function AddAssign(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::Addition, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function SubstractAssign(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::Subtraction, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function MultiplyAssign(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::Multiplication, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function DivideAssign(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::Division, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function ModulusAssign(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::Modulus, $AssignmentValueExpression);
    }
    
    /**
     * @return AssignmentExpression
     */
    final public function ConcatenateAssign(ValueExpression $AssignmentValueExpression) {
        return $this->AssignFrom(Operators\Assignment::Concatenate, $AssignmentValueExpression);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Variable Access">
    /**
     * @return PropertyExpression
     */
    final public function GetProperty(ValueExpression $NameValueExpression) {
        return new PropertyExpression($this, $NameValueExpression);
    }


    /**
     * @return IndexExpression
     */
    final public function GetIndex(ValueExpression $IndexValueExpression) {
        return new IndexExpression($this, $IndexValueExpression);
    }


    /**
     * @return MethodCallExpression
     */
    final public function CallMethod(ValueExpression $NameValueExpression, array $ArgumentValueExpressions = array()) {
        return new MethodCallExpression($this, $NameValueExpression, $ArgumentValueExpressions);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Helpers">
    /**
     * @return UnsetExpression
     */
    final public function UnsetThis() {
        return new UnsetExpression($this);
    }
    
    /**
     * @return IssetExpression
     */
    final public function ThisIsset() {
        return new IssetExpression($this);
    }
    
    /**
     * @return ValueStatementExpression
     */
    final public function AsStatement() {
        return new ValueStatementExpression($this);
    }
    // </editor-fold>
}

?>