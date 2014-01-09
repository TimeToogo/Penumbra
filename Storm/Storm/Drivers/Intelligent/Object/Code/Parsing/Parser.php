<?php

namespace Storm\Drivers\Intelligent\Object\Code\Parsing;

require __DIR__ . '/PHPParser/bootstrap.php';

use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;
 
class VariableResolver extends PHPParser_NodeVisitorAbstract {
    private $VariableValueMap;
    private $VariableExpressionMap = array();
    
    public function __construct(array $VariableValueMap) {
        $this->VariableValueMap = $VariableValueMap;
    }
        
    public function leaveNode(\PHPParser_Node $Node) {        
        $NodeType = str_replace('PHPParser_Node_Expr', '', $FullNodeName);

        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $Name = $Node->value;
                if(!is_string($Name)) {
                    throw new \Exception();
                }
                
                if(isset($this->VariableValueMap[$Name])) {
                    $Node->setAttribute('Value', $this->VariableValueMap[$Name]);
                }
                else if(isset($this->VariableExpressionMap[$Name])) {
                    return $this->VariableExpressionMap[$Name];
                }
                
            case strpos($Node->getType(), 'Expr_Assign') === 0:
                if($Node->var instanceof \PHPParser_Node_Expr_Variable) {
                    
                    return false;
                }
                break;
            
            default:
                return;
        }
    }
    
    private static $AssigmentBinaryOperatorMap = [
        'AssignBitwiseAnd' => Operators\Assignment::BitwiseAnd,
        'AssignBitwiseOr' => Operators\Assignment::BitwiseOr,
        'AssignBitwiseXor' => Operators\Assignment::BitwiseXor,
        'AssignConcat' => Operators\Assignment::Concatenate,
        'AssignDiv' => Operators\Assignment::Division,
        'AssignMinus' => Operators\Assignment::Subtraction,
        'AssignMod' => Operators\Assignment::Modulus,
        'AssignMul' => Operators\Assignment::Multiplication,
        'AssignPlus' => Operators\Assignment::Addition,
        'AssignShiftLeft' => Operators\Assignment::ShiftLeft,
        'AssignShiftRight' => Operators\Assignment::ShiftRight,
    ];
    
    private function AssignmentToExprNode(\PHPParser_Node_Expr $Node, $NodeTypeName) {
        if(!isset(self::$AssigmentBinaryOperatorMap[$NodeTypeName])) {
            
        }
        else {
            
        }
    }
}

class Parser {
    private $PHPParser;

    public function __construct() {
        $this->PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
    }
    
    public function Parse($PHPCode) {
        return $this->PHPParser->parse('<?php ' . $PHPCode . ' ?>');
    }
    
    public function ResolveVariables(array $StatementNodes, array $VariableMap) {
        foreach($StatementNodes as $StatementNode) {
            
        }
    }
    
    public function ParseNodes(array $Nodes) {
        return array_map([$this, 'ParseNode'], $Nodes);
    }
    
    public function ParseNode(\PHPParser_Node $Node) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr:
                return $this->ParseExpressionNode($Node);
                
            case $Node instanceof \PHPParser_Node_Scalar:
                return $this->ParseScalarNode($Node);
                
            default:
                throw new \Exception();
        }
    }

    private function VerifyNameNode(\PHPParser_Node $Node) {
        if(!($Node->class instanceof \PHPParser_Node_Name)) {
            throw new \Exception();
        }
        
        return $Node->toString();
    }
    
    // <editor-fold defaultstate="collapsed" desc="Expression node parsers">
    
    public function ParseExpressionNode(\PHPParser_Node_Expr $Node) {
        $FullNodeName = get_class($Node);
        $NodeType = str_replace('PHPParser_Node_Expr', '', $FullNodeName);
        
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Array:
                $ValueExpressions = array();
                foreach ($Node->items as $Key => $Item) {
                    $ValueExpressions[$Key] = $this->ParseNode($Item->value);
                }
                return Expression::NewArray($ValueExpressions);
                
            case isset(static::$AssignOperatorsMap[$NodeType]):
                return $this->ParseAssignNode($Node, $NodeType);
                
            case isset(static::$BinaryOperatorsMap[$NodeType]):
                return $this->ParseBinaryOperationNode($Node, $NodeType);
                
            case isset(static::$UnaryOperatorsMap[$NodeType]):
                return $this->ParseUnaryOperationNode($Node, $NodeType);
                
            case isset(static::$CastOperatorMap[$NodeType]):
                return $this->ParseCastNode($Node, $NodeType);
            
            case $Node instanceof \PHPParser_Node_Expr_ConstFetch:
                return Expression::Constant(constant($Node->name));
                
            case $Node instanceof \PHPParser_Node_Expr_ClassConstFetch:
                return Expression::Constant(constant($Node->class . '::' . $Node->name));
                
            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                return Expression::FunctionCall(
                        $this->VerifyNameNode($Node->name),
                        $this->ParseNodes($Node->args));
                
            case ($Node instanceof \PHPParser_Node_Expr_New):
                return Expression::Construct(
                        $this->VerifyNameNode($Node->class),
                        $this->ParseNodes($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_StaticCall:
                if(!is_string($Node->name)) {
                    throw new \Exception();
                }
                return Expression::MethodCall(
                        Expression::Object($this->VerifyNameNode($Node->class)),
                        $Node->name,
                        $this->ParseNodes($Node->args));
            
            case $Node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                if(!is_string($Node->class) || !is_string($Node->name)) {
                    throw new \Exception();
                }
                $ClassName = $Node->clas;
                return Expression::Constant($ClassName::${$Node->name});
                
            default:
                throw new \Exception();
        }
        if (false)
            die();
        else if ($Node instanceof \PHPParser_Node_Expr_MethodCall)
            return Expression::MethodCall(
                    $this->ParseNode($Node->var), 
                    $this->ParseNameNode($Node->name), 
                    $this->ParseNodes($Node->args));
        
        else if ($Node instanceof \PHPParser_Node_Expr_PropertyFetch)
            return Expression::Property(
                    $this->ParseNode($Node->var), 
                    $this->ParseNameNode($Node->name));
        
        else if ($Node instanceof \PHPParser_Node_Expr_Ternary)

            return Expression::Ternary(
                    $this->ParseNode($Node->cond),                     
                    $Node->if ? $this->ParseNode($Node->if) : null,                     
                    $this->ParseNode($Node->else));
        else if ($Node instanceof \PHPParser_Node_Expr_Variable) {
            if(!is_string($Node->name)) {
                throw new \Exception();
            }
            return is_string($Node->name) ?
                    Expression::Variable($Node->name) : $this->ParseNode($Node->name);
        }
        else
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

    // <editor-fold defaultstate="collapsed" desc="Class node parsers">
    private function ParseClassNode(\PHPParser_Node_Stmt_Class $Node) {
        $Properties = array_filter($Node->stmts, function ($Node) {
            return $Node instanceof \PHPParser_Node_Stmt_Property;
        });

        $Constants = array_filter($Node->stmts, function ($Node) {
            return $Node instanceof \PHPParser_Node_Stmt_ClassConst;
        });

        return Expression::ClassDeclaration(
                        $Node->name, 
                $Node->extends, 
                $Node->implements, 
                $this->ParseNodes($Properties), 
                $this->ParseNodes($Constants), 
                $this->ParseNodes($Node->getMethods()));
    }


    private function ParseInterfaceNode(\PHPParser_Node_Stmt_Interface $Node) {
        $Constants = array_filter($Node->stmts, function ($Node) {
            return $Node instanceof \PHPParser_Node_Stmt_ClassConst;
        });
        $Signatures = array();
        foreach ($Node->stmts as $MethodNode) {
            $Signatures[] = $this->ParseMethodSignatureNode($MethodNode);
        }


        return Expression::InterfaceDeclaration(
                        $Node->name,                 $Node->extends, 
                $this->ParseNodes($Constants), 
                $Signatures);
    }

    private function ParsePropertyNode(\PHPParser_Node_Stmt_Property $Node) {
        $PropertyExpressions = array();
        $AccessLevel = null;
        if ($Node->isPrivate())
            $AccessLevel = Members\AccessLevel::PrivateAccess;
        else if ($Node->isProtected())
            $AccessLevel = Members\AccessLevel::ProtectedAccess;
        else
            $AccessLevel = Members\AccessLevel::PublicAccess;

        $IsStatic = $Node->isStatic();

        foreach ($Node->props as $PropertyNode) {
            $PropertyExpressions[] = 
                    Expression::PropertyDeclaration($AccessLevel, $IsStatic, $PropertyNode->name, 
                            $this->ParseNode($PropertyNode->default));
        }

        return Expression::Multiple($PropertyExpressions);
    }

    private function ParseMethodNode(\PHPParser_Node_Stmt_ClassMethod $Node) {
        return Expression::MethodDeclaration(
                        $this->ParseMethodSignatureNode($Node), 
                 $this->ParseBodyAsBlock($Node));
    }


    private function ParseMethodSignatureNode(\PHPParser_Node_Stmt_ClassMethod $Node) {
        return Expression::MethodSignature(
                        $this->ParseNodeAccessLevel($Node), 
                $Node->isStatic(), 
                $Node->isFinal(), 
                $Node->isAbstract(), 
                $Node->name, 
                $this->ParseNodes($Node->params));
    }

    private function ParseFunctionNode(\PHPParser_Node_Stmt_Function $Node) {
        return Expression::FunctionDeclaration(
                        $Node->name, 
                 $this->ParseNodes($Node->params), 
                 $this->ParseBodyAsBlock($Node));
    }

    private function ParseFunctionParameter(\PHPParser_Node_Param $Node) {
        return Expression::Parameter(
                        (string) $Node->name, 
                $Node->type, 
                $Node->byRef, 
                $Node->default === null ? null : $this->ParseNode($Node));
    }

    private function ParseIfNode(\PHPParser_Node_Stmt_If $Node) {
        $ElseExpression = $Node->else ? $this->ParseBodyAsBlock($Node->else) : null;
        foreach (array_reverse($Node->elseifs) as $ElseIf) {
            $ElseExpression = 
                     Expression::Conditional(
                            $this->ParseNode($ElseIf->cond), 
                             $this->ParseBodyAsBlock($ElseIf), 
                             $ElseExpression);
        }
        return Expression::Conditional(
                        $this->ParseNode($Node->cond), 
            $this->ParseBodyAsBlock($Node), 
            $ElseExpression);
    }

    private function ParseNodeAccessLevel($Node) {
        $AccessLevel = null;
        if ($Node->isPrivate())
            $AccessLevel = Members\AccessLevel::PrivateAccess;
        else if ($Node->isProtected())
            $AccessLevel = Members\AccessLevel::ProtectedAccess;
        else
            $AccessLevel = Members\AccessLevel::PublicAccess;

        return $AccessLevel;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Body parse helpers">
    private function ParseBodyAsMultiple(\PHPParser_Node $Node) {
        $Expressions = $this->ParseNodes($Node->stmts);

        return Expression::Multiple($Expressions);
    }

    private function ParseBodyAsBlock(\PHPParser_Node $Node) {
        $Expressions = $this->ParseNodes($Node->stmts);

        return Expression::Block($Expressions);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Expression node maps">
    private static $UnaryOperatorsMap = [
        'BitwiseNot' => Operators\Unary::BitwiseNot,
        'BooleanNot' => Operators\Unary::Not,
        'ErrorSuppress' => Operators\Unary::ShutUp,
        'PostInc' => Operators\Unary::Increment,
        'PostDec' => Operators\Unary::Decrement,
        'PreInc' => Operators\Unary::PreIncrement,
        'PreDec' => Operators\Unary::PreDecrement,
        'UnaryMinus' => Operators\Unary::Negation,
    ];

    private function ParseUnaryOperationNode(\PHPParser_Node_Expr $Node, $NodeTypeName) {
        $OperatorInfo = static::$UnaryOperatorsMap[$NodeTypeName];
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
        'Cast_Int' => Operators\Cast::Int,
        'Cast_Object' => Operators\Cast::Object,
        'Cast_String' => Operators\Cast::String,
        'Cast_Unset' => Operators\Cast::UnsetCast,
    ];

    private function ParseCastNode(\PHPParser_Node_Expr_Cast $Node, $NodeTypeName) {
        return Expression::Cast(
                        static::$CastOperatorMap[$NodeTypeName], 
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
                static::$BinaryOperatorsMap[$NodeTypeName], 
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
                static::$AssignOperatorsMap[$NodeTypeName], 
                $this->ParseNode($Node->expr));
    }
    // </editor-fold>
}

?>
