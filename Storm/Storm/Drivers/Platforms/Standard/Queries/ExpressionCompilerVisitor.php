<?php

namespace Storm\Drivers\Platforms\Standard\Queries;

use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class ExpressionCompilerVisitor extends Queries\ExpressionCompilerVisitor {
    
    public function __construct(Queries\IExpressionOptimizer $ExpressionOptimizer) {
        parent::__construct($ExpressionOptimizer);
        
        $this->BinaryOperators = $this->BinaryOperators();
        $this->UnaryOperators = $this->UnaryOperators();
    }
    
    public function VisitMultiple(R\MultipleExpression $Expression) {
        $this->WalkAll($Expression->GetExpressions());
    }
    
    public function VisitBoundValue(R\BoundValueExpression $Expression) {
        $this->QueryBuilder->AppendSingleValue($Expression->GetValue());
    }
    
    protected function VisitEscapedValue(R\EscapedValueExpression $Expression) {
        $this->QueryBuilder->AppendSingleEscapedValue($Expression->GetValue());
    }
    
    public function VisitKeyword(R\KeywordExpression $Expression) {
        $this->QueryBuilder->Append(' ' . $Expression->GetString() . ' ');
    }
    
    public function VisitLiteral(R\LiteralExpression $Expression) {
        $this->QueryBuilder->Append($Expression->GetString());
    }
    
    public function VisitAlias(R\AliasExpression $Expression) {
        $this->Visit($Expression->GetValueExpression());
        $this->QueryBuilder->AppendIdentifier(' #', [$Expression->GetAlias()]);
    }
    
    public function VisitIdentifier(R\IdentifierExpression $Expression) {
        $this->QueryBuilder->AppendIdentifier('#', $Expression->GetIdentifierSegments());
    }
    
    public function VisitSubSelect(R\SubSelectExpression $Expression) {
        $this->QueryBuilder->Append('(');
        $this->QueryBuilder->AppendSelect($Expression->GetSelect());
        $this->QueryBuilder->Append(')');
    }
    
    public function VisitBinaryOperation(R\BinaryOperationExpression $Expression) {
        $this->QueryBuilder->Append('(');
        $this->Walk($Expression->GetLeftOperandExpression());
        $this->QueryBuilder->Append($this->GetBinaryOperatorString($Expression->GetOperator()));
        $this->Walk($Expression->GetRightOperandExpression());
        $this->QueryBuilder->Append(')');
    }

    public $BinaryOperators;
    protected abstract function BinaryOperators();

    public function GetBinaryOperatorString($Operator) {
        if (isset($this->BinaryOperators[$Operator])) {
            return ' ' . $this->BinaryOperators[$Operator] . ' ';
        }
        else {
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied binary operator: %s', 
                    get_class($this),
                    $Operator);
        }
    }
    
    public function VisitUnaryOperation(R\UnaryOperationExpression $Expression) {
        $this->QueryBuilder->Append('(');
        $this->QueryBuilder->Append($this->GetUnaryOperatorString($Expression->GetOperator()));
        $this->Walk($Expression->GetOperandExpression());
        $this->QueryBuilder->Append(')');
    }

    public $UnaryOperators;
    protected abstract function UnaryOperators();

    public function GetUnaryOperatorString($Operator) {
        if (isset($this->UnaryOperators[$Operator])) {
            return $this->UnaryOperators[$Operator];
        } 
        else {
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied unary operator: %s', 
                    get_class($this),
                    $Operator);
        }
    }
    
    public function VisitFunctionCall(R\FunctionCallExpression $Expression) {
        $this->QueryBuilder->Append($Expression->GetName());
        
        $this->QueryBuilder->Append('(');
        $First = true;
        foreach ($Expression->GetArgumentExpressions() as $ArgumentExpression) {
            if($First) $First = false;
            else
                $this->QueryBuilder->Append(', ');
            
            $this->Walk($ArgumentExpression);
        }
        $this->QueryBuilder->Append(')');
    }

    public function VisitCompundBoolean(R\CompoundBooleanExpression $Expression) {
        $this->QueryBuilder->Append('(');
        $LogicalOperatorString = $this->GetBinaryOperatorString($Expression->GetLogicalOperator());
        $BooleanExpressions = $Expression->GetBooleanExpressions();
        foreach ($this->QueryBuilder->Delimit($BooleanExpressions, $LogicalOperatorString) as $BooleanExpression) {
            $this->Walk($BooleanExpression);
        }
        $this->QueryBuilder->Append(')');
    }
    
    public function VisitValueList(R\ValueListExpression $Expression) {
        $this->QueryBuilder->Append('(');
        $First = true;
        foreach ($Expression->GetValueExpressions() as $ValueExpression) {
            if($First) $First = false;
            else
                $this->QueryBuilder->Append(', ');
            
            $this->Walk($ValueExpression);
        }
        $this->QueryBuilder->Append(')');
    }

    public function VisitIf(R\IfExpression $Expression) {
        $this->QueryBuilder->Append(' CASE ');
        
        $this->VisitBinaryOperation(
                R\Expression::BinaryOperation(
                        $Expression->GetConditionExpression(), 
                        R\Operators\Binary::LogicalAnd, 
                        R\Expression::BoundValue(1)));
        
        $this->QueryBuilder->Append(' WHEN 1 THEN ');
        $this->Walk($Expression->GetIfTrueExpression());
        
        $this->QueryBuilder->Append(' ELSE ');
        $this->Walk($Expression->GetIfFalseExpression());
        
        $this->QueryBuilder->Append(' END ');
    }
}

?>