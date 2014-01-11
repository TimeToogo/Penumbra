<?php

namespace Storm\Drivers\Intelligent\Object\Code\Parsing;

require __DIR__ . '/PHPParser/bootstrap.php';

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;

/*
 * TODO: Refactor (I wrote this while I jet lagged)
 */
class Parser {
    private $PHPParser;
    private $ValueMetadataResolverVisitor;
    private $ValueMetadataNodeTraverser;

    public function __construct() {
        $this->PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $this->ValueMetadataResolverVisitor = new ValueMetadataResolverVisitor();
        $this->ValueMetadataNodeTraverser = new \PHPParser_NodeTraverser();
        $this->ValueMetadataNodeTraverser->addVisitor($this->ValueMetadataResolverVisitor);
    }
    
    /**
     * @return \PHPParser_Node[]
     */
    public function Parse($PHPCode, array $VariableValueMap) {
        $Nodes = $this->PHPParser->parse('<?php ' . $PHPCode . ' ?>');
        
        $this->ValueMetadataResolverVisitor->SetVariableValueMap($VariableValueMap);
        $Nodes = $this->ValueMetadataNodeTraverser->traverse($Nodes);
        $this->ResolveVariables($Nodes);
        
        return $Nodes;
    }
    
    private function ResolveVariables(array &$Nodes) {
        $NodeTraverser = new \PHPParser_NodeTraverser();
        
        $VariableResolverVisitor = new VariableResolverVisitor();
        $NodeTraverser->addVisitor($VariableResolverVisitor);
        
        //TODO: get a life
        $Nodes = $NodeTraverser->traverse($Nodes);
    }
    
    private function ParseNodes(
            Object\EntityMap $EntityMap, 
            $EntityVariableName, 
            $PropertiesAreGetters,
            array $Nodes) {
        return array_map(
                function ($Node) use (&$EntityMap, &$EntityVariableName, &$PropertiesAreGetters) {
                    return $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node);
                }, $Nodes);
    }
    
    private function ParseNode(
            Object\EntityMap $EntityMap, 
            $EntityVariableName, 
            $PropertiesAreGetters,
            \PHPParser_Node $Node) {
        switch (true) {
            case $Node->hasAttribute('Value'):
                $Value = $Node->getAttribute('Value');
                return is_object($Value) ? Expression::Object($Value) : Expression::Constant($Value);
            
            case $Node instanceof \PHPParser_Node_Stmt:
                return $this->ParseStatmentNode($Node);
        
            case $Node instanceof \PHPParser_Node_Expr:
                return $this->ParseExpressionNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node);
                
            case $Node instanceof \PHPParser_Node_Scalar:
                return $this->ParseScalarNode($Node);
                
            case $Node instanceof \PHPParser_Node_Arg:
                return $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->value);
                
            default:
                throw new \Exception();
        }
    }

    private function VerifyNameNode(\PHPParser_Node $Node) {
        if(!($Node instanceof \PHPParser_Node_Name)) {
            throw new \Exception();
        }
        
        return $Node->toString();
    }
    
    // <editor-fold defaultstate="collapsed" desc="Expression node parsers">
    
    public function ParseExpressionNode(
            Object\EntityMap $EntityMap, 
            $EntityVariableName, 
            $PropertiesAreGetters,
            \PHPParser_Node_Expr $Node) {
        $FullNodeName = get_class($Node);
        $NodeType = str_replace('PHPParser_Node_Expr_', '', $FullNodeName);
        
        switch (true) {
            //Property detection: TODO: invocation
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                $NestedNode = $Node;
                while(isset($NestedNode->var)) {
                    if($NestedNode->var instanceof \PHPParser_Node_Expr_Variable 
                            && $NestedNode->var->name === $EntityVariableName) {
                        return $this->ParsePropertyNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node);
                    }
                    $NestedNode = $NestedNode->var;
                }
        }
        
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Array:
                $ValueExpressions = array();
                foreach ($Node->items as $Key => $Item) {
                    $ValueExpressions[$Key] = $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Item->value);
                }
                return Expression::NewArray($ValueExpressions);
                
            case isset(static::$AssignOperatorsMap[$NodeType]):
                return Expression::Assign(
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->var), 
                        self::$AssignOperatorsMap[$NodeType], 
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->expr));
                
            case isset(static::$BinaryOperatorsMap[$NodeType]):
                return Expression::BinaryOperation(
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->left), 
                        self::$BinaryOperatorsMap[$NodeType], 
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->right));
                
            case isset(static::$UnaryOperatorsMap[$NodeType]):
                return Expression::UnaryOperation( 
                        self::$UnaryOperatorsMap[$NodeType], 
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->expr));
                
            case isset(static::$CastOperatorMap[$NodeType]):
                return Expression::Cast(
                        self::$CastOperatorMap[$NodeType], 
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->expr));
            
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                return Expression::FunctionCall(
                        $this->VerifyNameNode($Node->name),
                        $this->ParseNodes($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->args));
                
            case ($Node instanceof \PHPParser_Node_Expr_New):
                return Expression::Construct(
                        $this->VerifyNameNode($Node->class),
                        $this->ParseNodes($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                if(!is_string($Node->name)) {
                    throw new \Exception();
                }
                return Expression::MethodCall(
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->var),
                        $Node->name,
                        $this->ParseNodes($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_StaticCall:
                if(!is_string($Node->name)) {
                    throw new \Exception();
                }
                return Expression::MethodCall(
                        Expression::Object($this->VerifyNameNode($Node->class)),
                        $Node->name,
                        $this->ParseNodes($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->args));
             
            case ($Node instanceof \PHPParser_Node_Expr_Ternary):
                $If = $Node->if ?: $Node->cond;
                return Expression::Ternary(
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->cond),
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $If),
                        $this->ParseNode($EntityMap, $EntityVariableName, $PropertiesAreGetters, $Node->else));
                                   
            default:
                throw new \Exception();
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Statement node parsers">
    private function ParseStatmentNode(\PHPParser_Node_Stmt $Node) {
        switch (true) {
                            
            default:
                throw new \Exception();
        }
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Property node parsers">
    
    private function ParsePropertyNode(Object\EntityMap $EntityMap, $EntityVariableName, $IsGetter, \PHPParser_Node_Expr $Node) {
        $NodeTraverser = new \PHPParser_NodeTraverser();
        $AccessorBuilderVisitor = new AccessorBuilderVisitor($EntityVariableName);
        $NodeTraverser->addVisitor($AccessorBuilderVisitor);
        $NodeTraverser->traverse([$Node]);
        
        $Accessor = $AccessorBuilderVisitor->GetAccessor();
        $Identifier = $IsGetter ? 
                $Accessor->GetGetterIdentifier() : $Accessor->GetSetterIdentifier();
        foreach($EntityMap->GetProperties() as $Property) {
            if($Property instanceof \Storm\Drivers\Base\Object\Properties\Property) {
                $OtherAccessor = $Property->GetAccessor();
                $OtherIdentifier = $IsGetter ? 
                        $OtherAccessor->GetGetterIdentifier() : $OtherAccessor->GetSetterIdentifier();
                
                if($Identifier === $OtherIdentifier) {
                    return Expression::Property($Property);
                }
            }
        }
        throw new \Exception();
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Scalar node parsers">
    private function ParseScalarNode(\PHPParser_Node_Scalar $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Scalar_DNumber:
            case $Node instanceof \PHPParser_Node_Scalar_LNumber:
            case $Node instanceof \PHPParser_Node_Scalar_String:
                return Expression::Constant($Node->value);
                
            default:
                throw new \Exception();
        }
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
        $OperatorInfo = self::$UnaryOperatorsMap[$NodeTypeName];
        $Operator = is_array($OperatorInfo) ? $OperatorInfo[0] : $OperatorInfo;
        $NodeFieldName = is_array($OperatorInfo) ? $OperatorInfo[1] : 'expr';
        return Expression::UnaryOperation(
                $this->ParseNode($Node->$NodeFieldName), 
                $Operator, 
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

class ValueMetadataResolverVisitor extends \PHPParser_NodeVisitorAbstract {
    private $VariableValueMap = array();
    
    public function SetVariableValueMap(array $VariableValueMap) {
        $this->VariableValueMap = $VariableValueMap;
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        $Value = null;
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = $Node->name;
                if(!isset($this->VariableValueMap[$Name])) {
                    return;
                }
                else {
                    $Value = $this->VariableValueMap[$Name];
                    break;
                }
                
            case $Node instanceof \PHPParser_Node_Scalar_DNumber:
            case $Node instanceof \PHPParser_Node_Scalar_LNumber:
            case $Node instanceof \PHPParser_Node_Scalar_String:
                $Value = $Node->value;
                break;
            
            case $Node instanceof \PHPParser_Node_Expr_ConstFetch:
                $Value = constant($Node->name);
                break;
                
            case $Node instanceof \PHPParser_Node_Expr_ClassConstFetch:
                $Value = constant($Node->class . '::' . $Node->name);
                break;
                        
            case $Node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                if(!is_string($Node->class) || !is_string($Node->name)) {
                    throw new \Exception();
                }
                $ClassName = $Node->class;
                $Value = $ClassName::${$Node->name};
                break;
                
            default:
                return;
        }
        $Node->setAttribute('Value', $Value);
    }
    
    public function afterTraverse(array $nodes) {
        $this->VariableValueMap = array();
    }
}

class VariableResolverVisitor extends \PHPParser_NodeVisitorAbstract {
    private $VariableExpressionMap = array();
    
    public function enterNode(\PHPParser_Node $Node) {
        $NodeType = str_replace('PHPParser_Node_Expr_', '', get_class($Node));

        switch (true) {
            case strpos($Node->getType(), 'Expr_Assign') === 0:
                if($Node->var instanceof \PHPParser_Node_Expr_Variable) {
                    $Name = $Node->var->name;
                    if(!is_string($Name)) {
                        throw new \Exception();
                    }
                    $AssignmentValue = $this->Recurse($this->AssignmentToExpressionNode($Node, $NodeType));
                    $this->VariableExpressionMap[$Name] = $AssignmentValue;
                    
                    return $Node->var;//Will be replace on leaveNode
                }
                break;
                
            default:
                return;
        }
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = $Node->name;
                if(!is_string($Name)) {
                    throw new \Exception();
                }
                
                if(isset($this->VariableExpressionMap[$Name])) {
                    return $this->VariableExpressionMap[$Name];
                }
            
            default:
                return;
        }
    }
    private function Recurse(\PHPParser_Node $Node) {
        if($Node instanceof \PHPParser_Node_Expr_Variable) {
            return $Node;
        }
        else {
            $Traverser = new \PHPParser_NodeTraverser();
            $Traverser->addVisitor($this);
            return $Traverser->traverse([$Node])[0];
        }
    }
    
    private static $AssigmentToBinaryNodeMap = [
        'AssignBitwiseAnd' => 'BitwiseAnd',
        'AssignBitwiseOr' => 'BitwiseOr',
        'AssignBitwiseXor' => 'BitwiseXor',
        'AssignConcat' => 'Concat',
        'AssignDiv' => 'Div',
        'AssignMinus' => 'Minus',
        'AssignMod' => 'Mod',
        'AssignMul' => 'Mul',
        'AssignPlus' => 'Plus',
        'AssignShiftLeft' => 'ShiftLeft',
        'AssignShiftRight' => 'ShiftRight',
    ];
    
    private function AssignmentToExpressionNode(\PHPParser_Node_Expr $Node, $NodeType) {
        if(!isset(self::$AssigmentToBinaryNodeMap[$NodeType])) {
            return $Node->expr;
        }
        else {
            $Name = $Node->var->name;
            $BinaryExpresiionNodeType = '\PHPParser_Node_Expr_' . self::$AssigmentToBinaryNodeMap[$NodeType];
            $CurrentExpression = isset($this->VariableExpressionMap[$Name]) ? 
                    $this->VariableExpressionMap[$Name] : $Node->var;
            return new $BinaryExpresiionNodeType($CurrentExpression, $Node->expr);
        }
    }
}

class AccessorBuilderVisitor extends \PHPParser_NodeVisitorAbstract {
    private $EntityVariableName;
    private $AccessorBuilder;
    
    public function __construct($EntityVariableName) {
        $this->EntityVariableName = $EntityVariableName;
        $this->AccessorBuilder = new \Storm\Drivers\Intelligent\Object\Properties\Accessors\Builder();
    }
    
    public function GetAccessor() {
        return $this->AccessorBuilder->GetAccessor();
    }
    
    public function leaveNode(\PHPParser_Node $Node) {
        switch (true) {
            //Field
            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
                $Name = $Node->name;
                if(!is_string($Name)) {
                    throw new \Exception();
                }
                $this->AccessorBuilder->$Name;
                break;
            
            //Method
            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                $Name = $Node->name;
                if(!is_string($Name)) {
                    throw new \Exception();
                }
                call_user_func_array([$this->AccessorBuilder, $Name], $this->GetNodeValues($Node->args));
                break;
                
            //Indexor
            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                $Name = $Node->name;
                if(!is_string($Name)) {
                    throw new \Exception();
                }
                $this->AccessorBuilder[$this->GetNodeValue($Node->dim)];
                break;
                
            //Invocation
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                if($Node->name instanceof \PHPParser_Node_Expr_Variable && 
                        $Node->name->name === $this->EntityVariableName) {
                    call_user_func_array($this->AccessorBuilder, $this->GetNodeValues($Node->args));
                }
                break;
            
            default:
                return;
        }
    }
    
    public function GetNodeValues(array $ArgumentNodes) {
        return array_map([$this, 'GetNodeValue'], $ArgumentNodes);
    }
    
    public function GetNodeValue(\PHPParser_Node $Node) {
        if(!$ArgumentNode->hasAttribute('Value')) {
            throw new \Exception();
        }
        else {
            return $ArgumentNode->getAttribute('Value');
        }
    }
}

?>
