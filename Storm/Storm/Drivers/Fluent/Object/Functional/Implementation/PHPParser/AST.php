<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser;

use \Storm\Drivers\Fluent\Object\Functional\ASTBase;
use \Storm\Drivers\Fluent\Object\Functional\INode;
use \Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\PHPParserConstantValueNode;
use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;
use \Storm\Core\Object\IProperty;
use \Storm\Drivers\Fluent\Object\Functional\ASTException;

require_once 'NodeSimplifiers.php';

class AST extends ASTBase {
    private $OriginalNodes = [];
    private $HasReturnNode;
    private $ReturnNodes = [];
    
    private $VariableExpander;
    
    private $NodeSimplifier = [];
    
    private $UnresolvedVariables = [];
    private $VariableResolverVisiter;
    private $VariableResolver;
    
    private $AccessorBuilderVisitor;
    private $AccessorBuilder;
    
    public function __construct(
            array $Nodes, 
            Object\IEntityMap $EntityMap,
            $EntityVariableName) {
        
        parent::__construct([], $EntityMap, $EntityVariableName);
        $this->OriginalNodes = $Nodes;
        $this->LoadNodes();
                
        $this->InitializeVisitors($EntityVariableName);
    }
    
    private function InitializeVisitors($EntityVariableName) {
        $this->VariableExpander = new \PHPParser_NodeTraverser();
        $this->VariableExpander->addVisitor(new Visitors\VariableExpanderVisitor($this->VariableExpander));
        
        $this->NodeSimplifier = new \PHPParser_NodeTraverser();
        $this->NodeSimplifier->addVisitor(new Visitors\SimplifierVisitor(GetNodeSimplifiers()));
        
        $this->VariableResolver = new \PHPParser_NodeTraverser();
        $this->VariableResolverVisiter = new Visitors\VariableResolverVisitor();
        $this->VariableResolver->addVisitor($this->VariableResolverVisiter);
        $this->VariableResolver->addVisitor(
                new Visitors\UnresolvedVariableVisitor(
                        $this->UnresolvedVariables, /* Ignore: */[$EntityVariableName]));
    
        $this->AccessorBuilderVisitor = new Visitors\AccessorBuilderVisitor($EntityVariableName);
        $this->AccessorBuilder = new \PHPParser_NodeTraverser();
        $this->AccessorBuilder->addVisitor($this->AccessorBuilderVisitor);
        
    }
    
    private function LoadNodes() {
        $this->ReturnNodes = [];
        $WrappedNodes = [];
        
        foreach($this->OriginalNodes as $Node) {
            $WrappedNode = new Node($Node);
            $WrappedNodes[] = $WrappedNode;
            
            if($Node instanceof \PHPParser_Node_Stmt_Return) {
                $this->ReturnNodes[] = $WrappedNode;
            }
        }
        $this->SetNodes($WrappedNodes);
        
        $this->HasReturnNode = count($this->ReturnNodes) > 0;
    }

    public function HasReturnNode() {
        return $this->HasReturnNode;
    }
    
    public function GetReturnNodes() {
        return $this->ReturnNodes;
    }

    private function Traverse(\PHPParser_NodeTraverserInterface $Traverser, $Reload = true) {
        $this->OriginalNodes = $Traverser->traverse($this->OriginalNodes);
        if($Reload) {
            $this->LoadNodes();
        }
    }
    
    public function ExpandVariables() {
        $this->Traverse($this->VariableExpander);
    }
    
    public function Simplify() {
        $this->Traverse($this->NodeSimplifier);
    }
    
    public function GetUnresolvedVariables() {
        return $this->UnresolvedVariables;
    }

    public function IsResolved() {
        return count($this->UnresolvedVariables) === 0;
    }
    
    public function ResolveVariables(array $VariableValueMap) {
        $this->VariableResolverVisiter->SetVariableValueMap($VariableValueMap);
        $this->Traverse($this->VariableResolver);
    }
    
    private function ParseNodesInternal(array $Nodes) {
        return array_map(function ($Node) { return $this->ParseNodeInternal($Node); }, $Nodes);
    }
    
    protected function ParseNodeAsExpression(INode $Node) {
        return $this->ParseNodeInternal($Node->GetOriginalNode());
    }
    
    protected function ParseNodeInternal(\PHPParser_Node $Node) {        
        switch (true) {
            case $Node instanceof PHPParserConstantValueNode:
                return $this->ParseResolvedValue($Node->Value);
                
            case $Node instanceof \PHPParser_Node_Stmt:
                return $this->ParseStatmentNode($Node);
        
            case $Node instanceof \PHPParser_Node_Expr:
                return $this->ParseExpressionNode($Node);
                
            //Irrelavent node, no call time pass by ref anymore :)
            case $Node instanceof \PHPParser_Node_Arg:
                return $this->ParseNodeInternal($Node->value);
                
            default:
                throw new ASTException(
                        'Unsupported node type: %s',
                        get_class($Node));
        }
    }
    
    private function ParseResolvedValue($Value) {
        if(is_object($Value)) {
            return Expression::Object($Value);
        }
        else if(is_array($Value)) {
            return Expression::NewArray(
                    array_map(
                            function ($Value) { 
                                return $this->ParseResolvedValue($Value); 
                            }, 
                            $Value));
        }
        else {
            return  Expression::Constant($Value);
        }
    }
    
    final public static function VerifyNameNode($Node) {
        if(!($Node instanceof \PHPParser_Node_) && !is_string($Node)) {
            throw $this->DynamicTraversalsAreDisallowed();
        }
        
        return is_string($Node) ? $Node : $Node->toString();
    }
    
    final public static function VerifyIndexNode($Node) {
        if(!($Node instanceof PHPParserConstantValueNode) && $Node !== null) {
            throw $this->DynamicTraversalsAreDisallowed();
        }
        
        return $Node === null ? null : $Node->Value;
    }
    
    private function DynamicTraversalsAreDisallowed() {
        return new ASTException(
                    'Dynamic function calls, method calls, indexers, property accessing are not supported');
    }
    
    // <editor-fold defaultstate="collapsed" desc="Expression node parsers">
    
    public function ParseExpressionNode(\PHPParser_Node_Expr $Node) {
        $FullNodeName = get_class($Node);
        $NodeType = str_replace('PHPParser_Node_Expr_', '', $FullNodeName);
        
        if($this->ActsUponEntityVariable($Node)) {
            $PropertyExpression = $this->ParsePropertyNode($Node);
            if($PropertyExpression !== null) {
                return $PropertyExpression;
            }
        }
        
        switch (true) {
            case $MappedNode = $this->ParseOperatorNode($Node, $NodeType):
                return $MappedNode;
                
            case $Node instanceof \PHPParser_Node_Expr_Array:
                $ValueExpressions = [];
                foreach ($Node->items as $Key => $Item) {
                    $ValueExpressions[$Key] = $this->ParseNodeInternal($Item->value);
                }
                return Expression::NewArray($ValueExpressions);
                
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                if($Node->name instanceof PHPParser_Node_Expr) {
                    return Expression::Invocation(
                            $this->ParseNodeInternal($Node->name),
                            $this->ParseNodesInternal($Node->args));
                }
                else {
                    return Expression::FunctionCall(
                            $this->VerifyNameNode($Node->name),
                            $this->ParseNodesInternal($Node->args));
                }
                
            case ($Node instanceof \PHPParser_Node_Expr_New):
                return Expression::Construct(
                        $this->VerifyNameNode($Node->class),
                        $this->ParseNodesInternal($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                return Expression::MethodCall(
                        $this->ParseNodeInternal($Node->var),
                        $this->VerifyNameNode($Node->name),
                        $this->ParseNodesInternal($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
                return Expression::Field(
                        $this->ParseNodeInternal($Node->var),
                        $this->VerifyNameNode($Node->name));
            
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                return Expression::Index(
                        $this->ParseNodeInternal($Node->var),
                        $this->VerifyIndexNode($Node->dim));
            
            case $Node instanceof \PHPParser_Node_Expr_StaticCall:
                return Expression::MethodCall(
                        Expression::Object($this->VerifyNameNode($Node->class)),
                        $this->VerifyNameNode($Node->name),
                        $this->ParseNodesInternal($Node->args));
             
            case ($Node instanceof \PHPParser_Node_Expr_Ternary):
                $If = $Node->if ?: $Node->cond;
                return Expression::Ternary(
                        $this->ParseNodeInternal($Node->cond),
                        $this->ParseNodeInternal($If),
                        $this->ParseNodeInternal($Node->else));
                     
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = $this->VerifyNameNode($Node->name);
                if($Name === $this->EntityVariableName) {
                    return Expression::Entity();
                }
                else {
                    throw new ASTException(
                            'Cannot parse AST with unresolvable variable: $%s',
                            $Name);
                }
                
            default:
                throw new ASTException(
                        'Cannot parse AST with unknown expression node: %s',
                        get_class($Node));
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Statement node parsers">
    
    private function ParseStatmentNode(\PHPParser_Node_Stmt $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Stmt_Return:
                return $this->ParseExpressionNode($Node->expr);
            
            default:
                throw new ASTException(
                        'Cannot parse AST with unknown statement node: %s',
                        get_class($Node));
        }
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Property node parsers">
    
    private function ActsUponEntityVariable(\PHPParser_Node_Expr $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                $ParentNode = $Node;
                
                while($ParentNode instanceof PHPParser_Node_Expr) {
                    if($ParentNode instanceof \PHPParser_Node_Expr_Variable 
                            && $ParentNode->name === $this->EntityVariableName) {
                        
                        return true;
                    }
                    $ParentNode = $ParentNode instanceof PHPParser_Node_Expr_FuncCall ?
                        $ParentNode->name : $ParentNode->var;
                }
        }
        
        return false;
    }
    
    
    private function ParsePropertyNode(\PHPParser_Node_Expr $Node) {
        if($this->EntityMap === null) {
            throw new ASTException(
                    'Cannot parse property node without setting the entity map',
                    get_class($Node));
        }
        
        $TraversalExpression = $this->ParseNodeInternal($Node);
        
        $AssignmentValueExpression = null;
        $Property = $this->GetPropertyByExpression($TraversalExpression, $AssignmentValueExpression);
        if($Property !== null) {
            return $this->ParsePropertyExpression($Property, $AssignmentValueExpression);
        }
    }
    
    private function ParsePropertyExpression(IProperty $Property, Expression $AssignmentValueExpression = null) {
        if($AssignmentValueExpression !== null) {
            return Expression::Assign(
                    Expression::Property($Property), 
                    Operators\Assignment::Equal, 
                    $AssignmentValueExpression);
        }
        else {
            return Expression::Property($Property);
        }
    }
    
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Operater node maps">
    
    private function ParseOperatorNode(\PHPParser_Node_Expr $Node, $NodeType) {
        switch (true) {
            case isset(self::$AssignOperatorsMap[$NodeType]):
                return Expression::Assign(
                        $this->ParseNodeInternal($Node->var), 
                        self::$AssignOperatorsMap[$NodeType], 
                        $this->ParseNodeInternal($Node->expr));
                
            case isset(self::$BinaryOperatorsMap[$NodeType]):
                return Expression::BinaryOperation(
                        $this->ParseNodeInternal($Node->left), 
                        self::$BinaryOperatorsMap[$NodeType], 
                        $this->ParseNodeInternal($Node->right));
                
            case isset(self::$UnaryOperatorsMap[$NodeType]):
                return Expression::UnaryOperation( 
                        self::$UnaryOperatorsMap[$NodeType], 
                        $this->ParseNodeInternal($Node->expr));
                
            case isset(self::$CastOperatorMap[$NodeType]):
                return Expression::Cast(
                        self::$CastOperatorMap[$NodeType], 
                        $this->ParseNodeInternal($Node->expr));
                
            default:
                return null;
        }
    }
    
    private static $UnaryOperatorsMap = [
        'BitwiseNot' => Operators\Unary::BitwiseNot,
        'BooleanNot' => Operators\Unary::Not,
        'PostInc' => Operators\Unary::Increment,
        'PostDec' => Operators\Unary::Decrement,
        'PreInc' => Operators\Unary::PreIncrement,
        'PreDec' => Operators\Unary::PreDecrement,
        'UnaryMinus' => Operators\Unary::Negation,
    ];

    private static $CastOperatorMap = [
        'Cast_Array' => Operators\Cast::ArrayCast,
        'Cast_Bool' => Operators\Cast::Boolean,
        'Cast_Double' => Operators\Cast::Double,
        'Cast_Int' => Operators\Cast::Integer,
        'Cast_Object' => Operators\Cast::Object,
        'Cast_String' => Operators\Cast::String,
    ];
    
    private static $BinaryOperatorsMap = [
        'BitwiseAnd' => Operators\Binary::BitwiseAnd,
        'BitwiseOr' => Operators\Binary::BitwiseOr,
        'BitwiseXor' => Operators\Binary::BitwiseXor,
        'ShiftLeft' => Operators\Binary::ShiftLeft,
        'ShiftRight' => Operators\Binary::ShiftRight,
        'BooleanAnd' => Operators\Binary::LogicalAnd,
        'BooleanOr' => Operators\Binary::LogicalOr,
        'LogicalAnd' => Operators\Binary::LogicalAnd,
        'LogicalOr' => Operators\Binary::LogicalOr,
        'Plus' => Operators\Binary::Addition,
        'Minus' => Operators\Binary::Subtraction,
        'Mul' => Operators\Binary::Multiplication,
        'Div' => Operators\Binary::Division,
        'Mod' => Operators\Binary::Modulus,
        'Concat' => Operators\Binary::Concatenation,
        'Instanceof' => Operators\Binary::IsInstanceOf,
        'Equal' => Operators\Binary::Equality,
        'Identical' => Operators\Binary::Identity,
        'NotEqual' => Operators\Binary::Inequality,
        'NotIdentical' => Operators\Binary::NonIdentity,
        'Smaller' => Operators\Binary::LessThan,
        'SmallerOrEqual' => Operators\Binary::LessThanOrEqualTo,
        'Greater' => Operators\Binary::GreaterThan,
        'GreaterOrEqual' => Operators\Binary::GreaterThanOrEqualTo,
    ];


    private static $AssignOperatorsMap = [
        'Assign' => Operators\Assignment::Equal,
        'AssignBitwiseAnd' => Operators\Assignment::BitwiseAnd,
        'AssignBitwiseOr' => Operators\Assignment::BitwiseOr,
        'AssignBitwiseXor' => Operators\Assignment::BitwiseXor,
        'AssignConcat' => Operators\Assignment::Concatenate,
        'AssignDiv' => Operators\Assignment::Division,
        'AssignMinus' => Operators\Assignment::Subtraction,
        'AssignMod' => Operators\Assignment::Modulus,
        'AssignMul' => Operators\Assignment::Multiplication,
        'AssignPlus' => Operators\Assignment::Addition,
        'AssignRef' => Operators\Assignment::EqualReference,
        'AssignShiftLeft' => Operators\Assignment::ShiftLeft,
        'AssignShiftRight' => Operators\Assignment::ShiftRight,
    ];

    // </editor-fold>
}

?>
