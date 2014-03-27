<?php

namespace Penumbra\Drivers\Platforms\Base\Queries;

use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\Queries\IExpressionCompiler;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

abstract class ExpressionCompilerVisitor extends R\ExpressionVisitor implements IExpressionCompiler {
    /**
     * @var IExpressionOptimizer|null 
     */
    private $ExpressionOptimizer;
    
    /**
     * @var QueryBuilder 
     */
    protected $QueryBuilder;
    
    public function __construct(IExpressionOptimizer $ExpressionOptimizer = null) {
        $this->ExpressionOptimizer = $ExpressionOptimizer;
    }
    
    public function Append(QueryBuilder $QueryBuilder, R\Expression $Expression) {
        if($this->ExpressionOptimizer !== null) {
            $Expression = $this->ExpressionOptimizer->Optimize($Expression);
        }
        
        $this->QueryBuilder = $QueryBuilder;
        $this->Walk($Expression);
    }
    
    public function VisitAlias(R\AliasExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\AliasExpression::GetType());
    }

    public function VisitBinaryOperation(R\BinaryOperationExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\BinaryOperationExpression::GetType());
    }

    public function VisitColumn(R\ColumnExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\ColumnExpression::GetType());
    }

    public function VisitCompundBoolean(R\CompoundBooleanExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\CompoundBooleanExpression::GetType());
    }

    public function VisitBoundValue(R\BoundValueExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\BoundValueExpression::GetType());
    }

    public function VisitFunctionCall(R\FunctionCallExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\FunctionCallExpression::GetType());
    }

    public function VisitIdentifier(R\IdentifierExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\IdentifierExpression::GetType());
    }

    public function VisitIf(R\IfExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\IfExpression::GetType());
    }

    public function VisitKeyword(R\KeywordExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\KeywordExpression::GetType());
    }

    public function VisitLiteral(R\LiteralExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\LiteralExpression::GetType());
    }

    public function VisitMultiple(R\MultipleExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\MultipleExpression::GetType());
    }

    public function VisitSubSelect(R\SubSelectExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\SubSelectExpression::GetType());
    }

    public function VisitUnaryOperation(R\UnaryOperationExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\UnaryOperationExpression::GetType());
    }

    public function VisitValueList(R\ValueListExpression $Expression) {
        throw $this->UnimplementedExpression(__METHOD__, R\ValueListExpression::GetType());
    }
    
    private function UnimplementedExpression($Method, $ExpressionType) {
        return new \Penumbra\Drivers\Base\Relational\PlatformException(
                'Cannot compiler expression of type %s: unimplemented method %s must be overridden in %s',
                $ExpressionType,
                $Method,
                get_class($this));
    }
}

?>