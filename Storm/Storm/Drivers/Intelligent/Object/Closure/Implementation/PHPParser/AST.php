<?php

namespace Storm\Drivers\Intelligent\Object\Closure\Implementation\PHPParser;

use \Storm\Drivers\Intelligent\Object\Closure\ASTBase;
use \Storm\Drivers\Intelligent\Object\Closure\INode;
use \Storm\Drivers\Intelligent\Object\Closure\Implementation\PHPParser\PHPParserConstantValueNode;
use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;
use \Storm\Drivers\Base\Object\Properties\Property;
use \Storm\Drivers\Base\Object\Properties\Accessors\Accessor;

class AST extends ASTBase {
    private $OriginalNodes = array();
    private $HasReturnNode;
    private $ReturnNodes = array();
    
    private $VariableExpander;
    
    private $UnresolvedVariables = array();
    private $VariableResolverVisiter;
    private $VariableResolver;
    
    private $AccessorBuilderVisitor;
    private $AccessorBuilder;
    
    public function __construct(
            array $Nodes, 
            Object\EntityMap $EntityMap,
            $EntityVariableName) {
        
        parent::__construct(array(), $EntityMap, $EntityVariableName);
        $this->OriginalNodes = $Nodes;
        $this->LoadNodes();
        
        $this->VariableExpander = new \PHPParser_NodeTraverser();
        $this->VariableExpander->addVisitor(new Visitors\VariableExpanderVisitor($this->VariableExpander));
        
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
        $this->ReturnNodes = array();
        $WrappedNodes = array();
        
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
                $Value = $Node->Value;
                return is_object($Value) ? Expression::Object($Value) : Expression::Constant($Value);
            
            case $Node instanceof \PHPParser_Node_Stmt:
                return $this->ParseStatmentNode($Node);
        
            case $Node instanceof \PHPParser_Node_Expr:
                return $this->ParseExpressionNode($Node);
        
            case $Node instanceof \PHPParser_Node_Arg:
                return $this->ParseNodeInternal($Node->value);
                
            default:
                throw new \Exception('Unknown node type: ' . get_class($Node));
        }
    }
    
    private function VerifyNameNode(\PHPParser_Node $Node) {
        if(!($Node instanceof \PHPParser_Node_Name)) {
            throw new \Exception('Dynamic function calls, method calls, property accessing... are not supported');
        }
        
        return $Node->toString();
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
            case $Node instanceof \PHPParser_Node_Expr_Array:
                $ValueExpressions = array();
                foreach ($Node->items as $Key => $Item) {
                    $ValueExpressions[$Key] = $this->ParseNodeInternal($Item->value);
                }
                return Expression::NewArray($ValueExpressions);
                
            case isset(static::$AssignOperatorsMap[$NodeType]):
                return Expression::Assign(
                        $this->ParseNodeInternal($Node->var), 
                        self::$AssignOperatorsMap[$NodeType], 
                        $this->ParseNodeInternal($Node->expr));
                
            case isset(static::$BinaryOperatorsMap[$NodeType]):
                return Expression::BinaryOperation(
                        $this->ParseNodeInternal($Node->left), 
                        self::$BinaryOperatorsMap[$NodeType], 
                        $this->ParseNodeInternal($Node->right));
                
            case isset(static::$UnaryOperatorsMap[$NodeType]):
                return Expression::UnaryOperation( 
                        self::$UnaryOperatorsMap[$NodeType], 
                        $this->ParseNodeInternal($Node->expr));
                
            case isset(static::$CastOperatorMap[$NodeType]):
                return Expression::Cast(
                        self::$CastOperatorMap[$NodeType], 
                        $this->ParseNodeInternal($Node->expr));
            
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                return Expression::FunctionCall(
                        $this->VerifyNameNode($Node->name),
                        $this->ParseNodesInternal($Node->args));
                
            case ($Node instanceof \PHPParser_Node_Expr_New):
                return Expression::Construct(
                        $this->VerifyNameNode($Node->class),
                        $this->ParseNodesInternal($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                if(!is_string($Node->name)) {
                    throw new \Exception();
                }
                return Expression::MethodCall(
                        $this->ParseNodeInternal($Node->var),
                        $Node->name,
                        $this->ParseNodesInternal($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_StaticCall:
                if(!is_string($Node->name)) {
                    throw new \Exception();
                }
                return Expression::MethodCall(
                        Expression::Object($this->VerifyNameNode($Node->class)),
                        $Node->name,
                        $this->ParseNodesInternal($Node->args));
             
            case ($Node instanceof \PHPParser_Node_Expr_Ternary):
                $If = $Node->if ?: $Node->cond;
                return Expression::Ternary(
                        $this->ParseNodeInternal($Node->cond),
                        $this->ParseNodeInternal($If),
                        $this->ParseNodeInternal($Node->else));
                     
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                throw new \Exception('Unresolved variable node: ' . $Node->name);
                
            default:
                throw new \Exception('Unknown node type');
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Statement node parsers">
    private function ParseStatmentNode(\PHPParser_Node_Stmt $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Stmt_Return:
                return $this->ParseExpressionNode($Node->expr);
            
            default:
                throw new \Exception();
        }
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Property node parsers">
    
    //Property detection: (does not support invocation yet)
    private function ActsUponEntityVariable(\PHPParser_Node_Expr $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                $NestedNode = $Node;
                while(isset($NestedNode->var)) {
                    if($NestedNode->var instanceof \PHPParser_Node_Expr_Variable 
                            && $NestedNode->var->name === $this->EntityVariableName) {
                        
                        return true;
                    }
                    $NestedNode = $NestedNode->var;
                }
        }
        
        return false;
    }
    
    private function ParsePropertyNode(\PHPParser_Node_Expr $Node) {
        $Properties = $this->EntityMap->GetProperties();
        
        $this->AccessorBuilder->traverse([$Node]);
        $Accessor = $this->AccessorBuilderVisitor->GetAccessor();
        $Identifier = $this->GetAccessorIdentifier($Accessor);
        
        $PropertyExpression = null;
        
        foreach($Properties as $Property) {
            if($Property instanceof Property) {
                $OtherAccessor = $Property->GetAccessor();
                $OtherIdentifier = $this->GetAccessorIdentifier($OtherAccessor);
                
                if($Identifier === $OtherIdentifier) {
                    $PropertyExpression = Expression::Property($Property);
                    break;
                }
            }
        }
        
        return $PropertyExpression;
    }
    
    private function GetAccessorIdentifier(Accessor $Accessor) {
        return $this->PropertyMode === self::PropertiesAreGetters ?
                $Accessor->GetGetterIdentifier() : $Accessor->GetSetterIdentifier();
    }
    
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Expression node maps">
    
    private static $UnaryOperatorsMap = [
        'BitwiseNot' => Operators\Unary::BitwiseNot,
        'BooleanNot' => Operators\Unary::Not,
        'PostInc' => Operators\Unary::Increment,
        'PostDec' => Operators\Unary::Decrement,
        'PreInc' => Operators\Unary::PreIncrement,
        'PreDec' => Operators\Unary::PreDecrement,
        'UnaryMinus' => Operators\Unary::Negation,
    ];

    private function ParseUnaryOperationNode(\PHPParser_Node_Expr $Node, $NodeTypeName) {
        return Expression::UnaryOperation(
                $this->ParseNode($Node->expr), 
                self::$UnaryOperatorsMap[$NodeTypeName], 
                $this->ParseNode($Node->right));
    }


    private static $CastOperatorMap = [
        'Cast_Array' => Operators\Cast::ArrayCast,
        'Cast_Bool' => Operators\Cast::Boolean,
        'Cast_Double' => Operators\Cast::Double,
        'Cast_Int' => Operators\Cast::Integer,
        'Cast_Object' => Operators\Cast::Object,
        'Cast_String' => Operators\Cast::String,
    ];

    private function ParseCastNode(\PHPParser_Node_Expr_Cast $Node, $NodeTypeName) {
        return Expression::Cast(
                self::$CastOperatorMap[$NodeTypeName], 
                $this->ParseNode($Node));
    }
    
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

    private function ParseBinaryOperationNode(\PHPParser_Node_Expr $Node, $NodeTypeName) {
        return Expression::BinaryOperation(
                $this->ParseNode($Node->left), 
                self::$BinaryOperatorsMap[$NodeTypeName], 
                $this->ParseNode($Node->right));
    }


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

    private function ParseAssignNode(\PHPParser_Node_Expr $Node, $NodeTypeName) {
        return Expression::Assignment(
                $this->ParseNode($Node->var), 
                self::$AssignOperatorsMap[$NodeTypeName], 
                $this->ParseNode($Node->expr));
    }
    // </editor-fold>
}

?>
