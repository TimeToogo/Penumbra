<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser;

use \Storm\Drivers\Fluent\Object\Functional\ASTBase;
use \Storm\Core\Object\Expressions\ExpressionTree;
use \Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\PHPParserResolvedValueNode;
use \Storm\Core\Object;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;
use \Storm\Drivers\Fluent\Object\Functional\ASTException;

require_once 'NodeSimplifiers.php';

class AST extends ASTBase {
    private $Nodes = [];
    
    private $ConstantValueNodeReplacer;
    
    private $AreVariablesResolved = false;
    private $VariableResolver;
    
    public function __construct(
            array $Nodes, 
            $EntityVariableName) {
        
        parent::__construct($EntityVariableName);
        $this->Nodes = $Nodes;
                
        $this->InitializeVisitors();
        $this->ResolveConstantValues();
    }
    
    private function InitializeVisitors() {
        $this->ConstantValueNodeReplacer = new \PHPParser_NodeTraverser();
        $this->ConstantValueNodeReplacer->addVisitor(new Visitors\ConstantValueNodeReplacerVisitor());
        
        $this->VariableResolver = new \PHPParser_NodeTraverser();
        $this->VariableResolver->addVisitor(new Visitors\VariableResolverVisitor($this->VariableResolver));
    }
    
    /**
     * Replaces all constant value nodes to the PHPParserResolvedValueNode for easy parsing.
     */
    private function ResolveConstantValues() {
        $this->Nodes = $this->ConstantValueNodeReplacer->traverse($this->Nodes);
    }
    
    /**
     * Removes all variables that can be removed:
     * 
     * function() use ($Unresolvable) {
     *     $Var = 4 + 5 - $Unresolvable;
     *     return 3 + $Var;
     * }
     * === resolves to ===
     * function() use ($Unresolvable) {
     *     4 + 5;
     *     return 3 + (4 + 5 - $Unresolvable)
     * }
     */
    private function ResolveVariables() {
        if(!$this->AreVariablesResolved) {
            $this->Traverse($this->VariableResolver);
            $this->AreVariablesResolved = true;
        }
    }
    
    public function GetExpressionTree() {
        $this->ResolveVariables();
        
        $Expressions = $this->ParseNodes($this->Nodes);
        return new ExpressionTree($Expressions);
    }

    private function Traverse(\PHPParser_NodeTraverserInterface $Traverser) {
        $this->Nodes = $Traverser->traverse($this->Nodes);
    }

    private function ParseNodes(array $Nodes) {
        return array_map(function ($Node) { return $this->ParseNode($Node); }, $Nodes);
    }
    
    private function ParseNode(\PHPParser_Node $Node) {        
        switch (true) {
            case $Node instanceof PHPParserResolvedValueNode:
                return $this->ParseResolvedValue($Node->Value);
                
            case $Node instanceof \PHPParser_Node_Stmt:
                return $this->ParseStatmentNode($Node);
        
            case $Node instanceof \PHPParser_Node_Expr:
                return $this->ParseExpressionNode($Node);
                
            //Irrelavent node, no call time pass by ref anymore :)
            case $Node instanceof \PHPParser_Node_Arg:
                return $this->ParseNode($Node->value);
                
            default:
                throw new ASTException(
                        'Unsupported node type: %s',
                        get_class($Node));
        }
    }
    
    private function ParseResolvedValue($Value) {
        return Expression::Value($Value);
    }
    
    final public static function VerifyNameNode($Node) {
        if(!($Node instanceof \PHPParser_Node_) && !is_string($Node)) {
            throw $this->DynamicTraversalsAreDisallowed();
        }
        
        return is_string($Node) ? $Node : $Node->toString();
    }
    
    final public static function VerifyIndexNode($Node) {
        if(!($Node instanceof PHPParserResolvedValueNode) && $Node !== null) {
            throw $this->DynamicTraversalsAreDisallowed();
        }
        
        return $Node === null ? null : $Node->Value;
    }
    
    //TODO: Remove restriction
    private function DynamicTraversalsAreDisallowed() {
        return new ASTException(
                    'Dynamic variable, function calls, method calls, indexers and property accessing is not supported');
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
                return $this->ParseArrayNode($Node);
                
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                return $this->ParseFunctionCallNode($Node);
                
            case ($Node instanceof \PHPParser_Node_Expr_New):
                return Expression::Constructor(
                        $this->VerifyNameNode($Node->class),
                        $this->ParseNodes($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                return Expression::MethodCall(
                        $this->ParseNode($Node->var),
                        $this->VerifyNameNode($Node->name),
                        $this->ParseNodes($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
                return Expression::Field(
                        $this->ParseNode($Node->var),
                        $this->VerifyNameNode($Node->name));
            
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                return Expression::Index(
                        $this->ParseNode($Node->var),
                        $this->VerifyIndexNode($Node->dim));
            
            case $Node instanceof \PHPParser_Node_Expr_StaticCall:
                return Expression::FunctionCall(
                        $this->VerifyNameNode($Node->class) . '::' . $this->VerifyNameNode($Node->name),
                        $this->ParseNodes($Node->args));
             
            case ($Node instanceof \PHPParser_Node_Expr_Ternary):
                return $this->ParseTernaryNode($Node);
                     
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = $this->VerifyNameNode($Node->name);
                if($Name === $this->EntityVariableName) {
                    return Expression::Entity();
                }
                else {
                    return Expression::UnresolvedVariable($Name);
                }
                
            default:
                throw new ASTException(
                        'Cannot parse AST with unknown expression node: %s',
                        get_class($Node));
        }
    }
    
    private function ParseArrayNode(\PHPParser_Node_Expr_FuncCall $Node) {
        $KeyExpressions = [];
        $ValueExpressions = [];
        foreach ($Node->items as $Key => $Item) {
            //Keys must match
            $KeyExpressions[$Key] = $this->ParseNode($Item->key);
            $ValueExpressions[$Key] = $this->ParseNode($Item->value);
        }
        return Expression::NewArray($KeyExpressions, $ValueExpressions);
    }
    
    private function ParseFunctionCallNode(\PHPParser_Node_Expr_Ternary $Node) {
        if($Node->name instanceof PHPParser_Node_Expr) {
            return Expression::Invocation(
                    $this->ParseNode($Node->name),
                    $this->ParseNodes($Node->args));
        }
        else {
            return Expression::FunctionCall(
                    $this->VerifyNameNode($Node->name),
                    $this->ParseNodes($Node->args));
        }
    }
    
    private function ParseTernaryNode(\PHPParser_Node_Expr_Array $Node) {
        //Imply omitted if value
        $If = $Node->if ?: $Node->cond;
        return Expression::Ternary(
                $this->ParseNode($Node->cond),
                $this->ParseNode($If),
                $this->ParseNode($Node->else));
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Statement node parsers">
    
    private function ParseStatmentNode(\PHPParser_Node_Stmt $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Stmt_Return:
                return Expression::ReturnExpression(
                        $Node->expr !== null ? $this->ParseNode($Node->expr) : null);
            
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
    
    /**
     * @param \PHPParser_Node_Expr $Node
     * @return O\PropertyExpression
     */
    private function ParsePropertyNode(\PHPParser_Node_Expr $Node) {
        if($this->EntityMap === null) {
            throw new ASTException(
                    'Cannot parse property node without the entity map');
        }
        
        $TraversalExpression = $this->ParseNode($Node);
        
        $ResolvedPropertyExpression = $this->EntityMap->ResolveTraversalExpression($TraversalExpression);
        return $ResolvedPropertyExpression;
    }
    
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Operater node maps">
    
    private function ParseOperatorNode(\PHPParser_Node_Expr $Node, $NodeType) {
        switch (true) {
            case isset(self::$AssignOperatorsMap[$NodeType]):
                return Expression::Assign(
                        $this->ParsePropertyNode($Node), 
                        self::$AssignOperatorsMap[$NodeType], 
                        $this->ParseNode($Node->expr));
                
            case isset(self::$BinaryOperatorsMap[$NodeType]):
                return Expression::BinaryOperation(
                        $this->ParseNode($Node->left), 
                        self::$BinaryOperatorsMap[$NodeType], 
                        $this->ParseNode($Node->right));
                
            case isset(self::$UnaryOperatorsMap[$NodeType]):
                return Expression::UnaryOperation( 
                        self::$UnaryOperatorsMap[$NodeType], 
                        $this->ParseNode($Node->expr));
                
            case isset(self::$CastOperatorMap[$NodeType]):
                return Expression::Cast(
                        self::$CastOperatorMap[$NodeType], 
                        $this->ParseNode($Node->expr));
                
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
