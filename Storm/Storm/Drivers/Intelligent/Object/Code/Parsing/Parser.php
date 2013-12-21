<?php

namespace Storm\Drivers\Intelligent\Object\Code\Parsing;

require __DIR__ . '/PHPParser/bootstrap.php';

use \Storm\Drivers\Intelligent\Object\Code\Expressions\Expression;
use \Storm\Drivers\Intelligent\Object\Code\Expressions\Members;
use \Storm\Drivers\Intelligent\Object\Code\Expressions\Operators;
 
class Parser {
    private $PHPParser;

    public function __construct() {
        $this->PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
    }

    /**
     * @return Expression
     */
    public function GenerateExpression($Code) {
        $AST = $this->PHPParser->parse($Code);
        $Expressions = $this->ParseNodes($AST);
        
        if(count($Expressions) === 1) {
            return Expression::Multiple($Expressions);
        }
        else {
            return $Expressions[0];
        }
    }

    private function ParseNodes(array $Nodes) {
        $Expressions = array();
        foreach($Nodes as $Node) {
            $Expressions[] = $this->ParseNode($Node);
        }

        return $Expressions;
    }

    private function ParseNode(\PHPParser_Node $Node) {
        if(false) die();
        else if($Node instanceof \PHPParser_Node_Stmt) 
            return $this->ParseStatmentNode($Node);
        else if($Node instanceof \PHPParser_Node_Expr) 
            return $this->ParseExpressionNode($Node);
        else if($Node instanceof \PHPParser_Node_Scalar) 
            return $this->ParseScalarNode($Node);
        else if($Node instanceof \PHPParser_Node_Name) 
            return Expression::Name((string)$Node);
        else if($Node instanceof \PHPParser_Node_Const) 
            return Expression::ConstantDeclaration($Node->name, $Node->value);
        else if($Node instanceof \PHPParser_Node_Param) 
            return Expression::Parameter(
                    $Node->name, 
                    $Node->type ? (string)$Node->type : null,
                    $Node->byRef, 
                    $Node->default ? $this->ParseNode($Node->default) : null);
        else if($Node instanceof \PHPParser_Node_Arg) 
            return $this->ParseNode($Node->value);
        else
            throw new Exception();
    }

    // <editor-fold defaultstate="collapsed" desc="Statement node parsers">
    private function ParseStatmentNode(\PHPParser_Node_Stmt $Node) {
        if (false)
            die();
        else if ($Node instanceof \PHPParser_Node_Stmt_Break)
            return Expression::BreakStatment($Node->num ? $this->ParseNode($Node->num) : null);
        else if ($Node instanceof \PHPParser_Node_Stmt_Case) {
            if ($Node->cond === null)
                return Expression::SwitchDefault($this->ParseBodyAsMultiple($Node));
            else
                return Expression::SwitchCase($this->ParseExpressionNode($Node->cond), 
                        $this->ParseBodyAsMultiple($Node));
        }
        else if ($Node instanceof \PHPParser_Node_Stmt_Catch)
            return Expression::CatchBlock(Expression::Parameter($Node->type, $Node->name),                  $this->ParseBodyAsBlock($Node));
        else if ($Node instanceof \PHPParser_Node_Stmt_Class)
            return $this->ParseClassNode($Node);
        else if ($Node instanceof \PHPParser_Node_Stmt_ClassConst)
            return Expression::Multiple($this->ParseNodes($Node->consts));
        else if ($Node instanceof \PHPParser_Node_Stmt_Const)
            return Expression::Multiple($this->ParseNodes($Node->consts));
        else if ($Node instanceof \PHPParser_Node_Stmt_ClassMethod)
            return $this->ParseMethodNode($Node);
        else if ($Node instanceof \PHPParser_Node_Stmt_Continue)
            return Expression::ContinueStatment($Node->num ? $this->ParseNode($Node->num) : null);
        else if ($Node instanceof \PHPParser_Node_Stmt_Do)
            return Expression::DoWhileLoop($this->ParseNode($Node), 
                    $this->ParseBodyAsBlock($Node));
        else if ($Node instanceof \PHPParser_Node_Stmt_Echo)
            Expression::EchoStatement($this->ParseNodes($Node->exprs));
        else if ($Node instanceof \PHPParser_Node_Stmt_For)
            return Expression::ForLoop($this->ParseBodyAsBlock($Node), 
                    $Node->init ? $this->ParseNode($Node->init[0]) : null,                     $Node->cond ? $this->ParseNode($Node->cond[0]) : null,                     $Node->loop ? $this->ParseNode($Node->loop[0]) : null);
        else if ($Node instanceof \PHPParser_Node_Stmt_Foreach)
            return Expression::ForeachLoop(
                            $this->ParseNode($Node->exprs),                     $this->ParseNode($Node->valueVar),                     $this->ParseBodyAsBlock($Node),                     $Node->keyVar ? $this->ParseNode($Node->keyVar) : null);
        else if ($Node instanceof \PHPParser_Node_Stmt_Function)
            return $this->ParseFunctionNode($Node);
        else if ($Node instanceof \PHPParser_Node_Stmt_Global)
            return Expression::GlobalVariableDeclaration($this->ParseNodes($Node->vars));
        else if ($Node instanceof \PHPParser_Node_Stmt_Goto)
            return Expression::GotoStatement($Node->name);
        else if ($Node instanceof \PHPParser_Node_Stmt_If)
            return $this->ParseIfNode($Node);
        else if ($Node instanceof \PHPParser_Node_Stmt_Interface)
            return $this->ParseInterfaceNode($Node);
        else if ($Node instanceof \PHPParser_Node_Stmt_Label)
            return Expression::Label($Node->name);

        else if ($Node instanceof \PHPParser_Node_Stmt_Namespace)
            return Expression::NamespaceDeclaration($Node->name->toString(), $this->ParseNodes($Node->stmts));
        else if ($Node instanceof \PHPParser_Node_Stmt_Property)
            return $this->ParsePropertyNode($Node);
        else if ($Node instanceof \PHPParser_Node_Stmt_Return)
            return Expression::ReturnStatement($Node->expr ? $this->ParseNode($Node->expr) : null);
        else if ($Node instanceof \PHPParser_Node_Stmt_Static)
            return Expression::Multiple($this->ParseNodes($Node->vars));
        else if ($Node instanceof \PHPParser_Node_Stmt_StaticVar)
            return Expression::StaticVariableDeclaration($Node->name, 
                    $Node->default ? $this->ParseNode($Node->default) : null);
        else if ($Node instanceof \PHPParser_Node_Stmt_Switch) {
            $Cases = $this->ParseNodes(
                    array_filter($Node->cases, function ($Case) {
                        return $Case->cond !== null;
                    }));
            $DefaultNode = 
                    array_filter($Node->cases, function ($Case) {
                return $Case->cond === null;
            });


            return Expression::SwitchStructure(
                            $this->ParseNode($Node->cond), 
                    $Cases, 
                    $DefaultNode ? $this->ParseNode($DefaultNode) : null);
        }         else if ($Node instanceof \PHPParser_Node_Stmt_Throw)
            return Expression::ThrowStatement($this->ParseNode($Node->expr));
        else if ($Node instanceof \PHPParser_Node_Stmt_TryCatch)
            return Expression::TryBlock(
                            $this->ParseBodyAsBlock($Node), 
                    $this->ParseNodes($Node->catches));
        else if ($Node instanceof \PHPParser_Node_Stmt_Unset)
            return Expression::UnsetValues($this->ParseNodes($Node->vars));
        else if ($Node instanceof \PHPParser_Node_Stmt_Use)
            return Expression::Multiple($this->ParseNodes($Node->uses));
        else if ($Node instanceof \PHPParser_Node_Stmt_UseUse)
            return Expression::UseDeclaration($Node->name, $Node->alias);
        else if ($Node instanceof \PHPParser_Node_Stmt_While)
            return Expression::WhileLoop(
                            $this->ParseNode($Node->cond), 
                    $this->ParseBodyAsBlock($Node));
        else

            throw new Exception();
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Expression node parsers">
    private function ParseExpressionNode(\PHPParser_Node_Expr $Node) {
        $FullNodeName = get_class($Node);
        $NodeType = str_replace('PHPParser_Node_Expr', '', $FullNodeName);
        
        if (false)
            die();
        else if ($Node instanceof \PHPParser_Node_Expr_Array) {
            $KeyValueExpressions = array();
            $ValueExpressions = array();
            foreach ($Node->items as $Key => $Item) {
                $ValueExpressions[$Key] = $this->ParseNode($Item->value);
                if (isset($Item->key))
                    $KeyValueExpressions[$Key] = $this->ParseNode($Item->key);
            }


            return Expression::NewArray(
                            $KeyValueExpressions, 
                    $ValueExpressions);
        }
        else if ($Node instanceof \PHPParser_Node_Expr_ArrayDimFetch)

            return Expression::Index(
                            $this->ParseNode($Node->var),                     $this->ParseNode($Node->dim));
        else if (isset(static::$AssignOperatorsMap[$NodeType]))
            return $this->ParseAssignNode($Node, $NodeType);
        else if (isset(static::$BinaryOperatorsMap[$NodeType]))
            return $this->ParseBinaryOperationNode($Node, $NodeType);
        else if (isset(static::$UnaryOperatorsMap[$NodeType]))
            return $this->ParseUnaryOperationNode($Node, $NodeType);
        else if (isset(static::$CastOperatorMap[$NodeType]))
            return $this->ParseCastNode($Node, $NodeType);
        else if ($Node instanceof \PHPParser_Node_Expr_ClassConstFetch)

            return Expression::MemberConstant(
                            $this->ParseNode($Node->class), 
                    $Node->name);
        else if ($Node instanceof \PHPParser_Node_Expr_Clone)

            return Expression::CloneValue($this->ParseNode($Node->expr));
        else if ($Node instanceof \PHPParser_Node_Expr_Closure)

            return Expression::Closure(
                            $Node->static,                     $this->ParseNodes($Node->params), 
                    $this->ParseNodes($Node->uses), 
                    $Node->byRef,                     $this->ParseBodyAsBlock($Node));
        else if ($Node instanceof \PHPParser_Node_Expr_ClosureUse)

            return Expression::UsedVariable($Node->var, $Node->byRef);
        else if ($Node instanceof \PHPParser_Node_Expr_ConstFetch)

            return Expression::NamedConstant($Node->name);
        else if (isset(static::$ConstructFunctionMap[$NodeType]))
            return $this->ParseConstructNode($Node, $NodeType);
        else if ($Node instanceof \PHPParser_Node_Expr_FuncCall)

            return Expression::FunctionCall(
                            $this->ParseNode($Node->name), 
                    $this->ParseNode($Node->args));
        else if ($Node instanceof \PHPParser_Node_Expr_List)

            return Expression::ListVariables($this->ParseNodes($Node->vars));
        else if ($Node instanceof \PHPParser_Node_Expr_MethodCall)

            return Expression::MethodCall(
                            $this->ParseNode($Node->var), 
                    $this->ParseNameNode($Node->name), 
                    $this->ParseNodes($Node->args));
        else if ($Node instanceof \PHPParser_Node_Expr_New)

            return Expression::NewInstance(
                            $this->ParseNode($Node->class), 
                    $this->ParseNodes($Node->args));
        else if ($Node instanceof \PHPParser_Node_Expr_PropertyFetch)

            return Expression::Property(
                            $this->ParseNode($Node->var), 
                    $this->ParseNameNode($Node->name));
        else if ($Node instanceof \PHPParser_Node_Expr_StaticCall)

            return Expression::StaticMethodCall(
                            $this->ParseNode($Node->class),                     $this->ParseNameNode($Node->name),                     $this->ParseNodes($Node->args));
        else if ($Node instanceof \PHPParser_Node_Expr_StaticPropertyFetch)

            return Expression::StaticProperty(
                            $this->ParseNode($Node->class),                     $this->ParseNameNode($Node->name));
        else if ($Node instanceof \PHPParser_Node_Expr_Ternary)

            return Expression::Ternary(
                            $this->ParseNode($Node->cond),                     $Node->if ? $this->ParseNode($Node->if) : null,                     $this->ParseNode($Node->else));
        else if ($Node instanceof \PHPParser_Node_Expr_Variable)
            return is_string($Node->name) ?
                    Expression::Variable($Node->name) : $this->ParseNode($Node->name);
        else
            throw new Exception();
    }

    private function ParseNameNode($Node) {
        return is_string($Node) ?

                Expression::Name($Node) : $this->ParseNode($Node);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Scalar node parsers">
    private function ParseScalarNode(\PHPParser_Node_Scalar $Node) {
        if (false)
            die();
        else if ($Node instanceof \PHPParser_Node_Scalar_ClassConst)
            return Expression::NamedConstant('__CLASS__');
        else if ($Node instanceof \PHPParser_Node_Scalar_DirConst)
            return Expression::NamedConstant('__DIR__');
        else if ($Node instanceof \PHPParser_Node_Scalar_FileConst)
            return Expression::NamedConstant('__FILE__');
        else if ($Node instanceof \PHPParser_Node_Scalar_FuncConst)
            return Expression::NamedConstant('__FUNCTION__');
        else if ($Node instanceof \PHPParser_Node_Scalar_LineConst)
            return Expression::NamedConstant('__LINE__');
        else if ($Node instanceof \PHPParser_Node_Scalar_MethodConst)
            return Expression::NamedConstant('__METHOD__');
        else if ($Node instanceof \PHPParser_Node_Scalar_TraitConst)
            return Expression::NamedConstant('__TRAIT__');
        else if ($Node instanceof \PHPParser_Node_Scalar_DNumber)
            return Expression::Constant($Node->value);
        else if ($Node instanceof \PHPParser_Node_Scalar_LNumber)
            return Expression::Constant($Node->value);
        else if ($Node instanceof \PHPParser_Node_Scalar_String)
            return Expression::Constant($Node->value);
        else if ($Node instanceof \PHPParser_Node_Scalar_String)
            return Expression::Constant($Node->value);
        else
            throw new Exception();
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


    private static $ConstructFunctionMap = [
        'Empty' => 'empty',
        'Eval' => 'eval',
        'Exit' => 'exit',
        'Print' => 'print'
    ];

    private function ParseConstructNode(\PHPParser_Node_Expr $Node, $NodeTypeName) {
        return Expression::FunctionCall(
                        static::$ConstructFunctionMap[$NodeTypeName], 
                $Node->expr ? [$this->ParseNode($Node->expr)] : null);
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
