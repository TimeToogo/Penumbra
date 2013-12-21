<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

abstract class Expression {
    use \Storm\Core\Helpers\Type;
    
    final public function Compile() {
        $Code = '';
        $this->CompileCode($Code);
        
        return $Code;
    }
    
    protected abstract function CompileCode(&$Code);
    
    final public function __toString() {
        return $this->Compile();
    }
    
    // <editor-fold defaultstate="collapsed" desc="Factory Methods">
    /**
     * @return AssignmentExpression
     */
    final public static function Assignment(ValueExpression $AssignToValueExpression, $AssignmentOperator, ValueExpression $AssignmentValueExpression) {
        return new AssignmentExpression($AssignToValueExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    final public static function BinaryOperation(ValueExpression $LeftOperandExpression, $Operator, ValueExpression $RightOperandExpression) {
        return new BinaryOperationExpression($LeftOperandExpression, $Operator, $RightOperandExpression);
    }
    
    /**
     * @return BlockExpression
     */
    final public static function Block(array $Expressions) {
        return new BlockExpression($Expressions);
    }
    
    /**
     * @return BreakExpression
     */
    final public static function BreakStatment(ValueExpression $ValueExpression = null) {
        return new BreakExpression($ValueExpression);
    }
    
    /**
     * @return CaseExpression
     */
    final public static function SwitchCase(ValueExpression $CaseValueExpression, Expression $IfCaseExpression) {
        return new CaseExpression($CaseValueExpression, $IfCaseExpression);
    }
    
    /**
     * @return CatchExpression
     */
    final public static function CatchBlock(ParameterExpression $ExceptionParameterExpression, BlockExpression $BodyExpression) {
        return new CatchExpression($ExceptionParameterExpression, $BodyExpression);
    }
    
    /**
     * @return CastExpression
     */
    final public static function Cast($CastType, ValueExpression $CastValueExpression) {
        return new CastExpression($CastType, $CastValueExpression);
    }
    
    /**
     * @return ClassExpression
     */
    final public static function ClassDeclaration($Name, $ParentClass = null, array $ImplementedInterfaces = array(), 
            array $PropertyDeclarationExpressions = array(), 
            array $ConstantDeclarationExpressions = array(), 
            array $MethodDeclarationExpressions = array()) {
        return new ClassExpression($Name, $ParentClass, $ImplementedInterfaces, 
                $PropertyDeclarationExpressions, 
                $ConstantDeclarationExpressions, 
                $MethodDeclarationExpressions);
    }
    
    /**
     * @return CloneExpression
     */
    final public static function CloneValue(ValueExpression $CloneValueExpression) {
        return new CloneExpression($CloneValueExpression);
    }
    
    /**
     * @return ClosureExpression
     */
    final public static function Closure($IsStatic, array $ParameterExpressions, array $UsedVariableExpressions,
            $ReturnByReference, BlockExpression $BodyExpression) {
        return new ClosureExpression($IsStatic, $ParameterExpressions, 
                $UsedVariableExpressions, $ReturnByReference, $BodyExpression);
    }
    
    /**
     * @return ConditionalExpression
     */
    final public static function Conditional(ValueExpression $ConditionExpression, 
            Expression $IfTrueExpression, Expression $ElseExpression = null) {
        return new ConditionalExpression($ConditionExpression, $IfTrueExpression, $ElseExpression);
    }
    const FDSO = 342;
    /**
     * @return ConstantDeclarationExpression
     */
    final public static function ConstantDeclaration($Name, $Value) {
        return new ConstantDeclarationExpression($Name, $Value);
    }
    
    /**
     * @return ContinueExpression
     */
    final public static function ContinueStatment(ValueExpression $ValueExpression = null) {
        return new ContinueExpression($ValueExpression);
    }
    
    /**
     * @return DefaultExpression
     */
    final public static function SwitchDefault(Expression $BodyExpression) {
        return new DefaultExpression($BodyExpression);
    }
    
    /**
     * @return DoWhileLoopExpression
     */
    final public static function DoWhileLoop(ValueExpression $ConditionalExpression, BlockExpression $BodyExpression) {
        return new DoWhileLoopExpression($ConditionalExpression, $BodyExpression);
    }
    
    /**
     * @return EchoExpression
     */
    final public static function EchoStatement(array $ValueExpressions) {
        return new EchoExpression($ValueExpressions);
    }
    
    /**
     * @return ForLoopExpression
     */
    final public static function ForLoop(Expression $BodyExpression, 
            StatementExpression $FirstExpression = null, 
            StatementExpression $SecondExpression = null, 
            StatementExpression $ThirdExpression = null) {
        return new ForLoopExpression($BodyExpression, $FirstExpression, $SecondExpression, $ThirdExpression);
    }
    
    /**
     * @return ForeachLoopExpression
     */
    final public static function ForeachLoop(ValueExpression $TraversableValueExpression, 
            ValueExpression $AsValueExpression, Expression $BodyExpression,
            ValueExpression $KeyValueExpression = null) {
        return new ForeachLoopExpression($TraversableValueExpression, $AsValueExpression, $BodyExpression, $KeyValueExpression);
    }
    
    /**
     * @return FunctionCallExpression
     */
    final public static function FunctionCall(ValueExpression $NameValueExpression, array $ArgumentValueExpressions = array()) {
        return new FunctionCallExpression($NameValueExpression, $ArgumentValueExpressions);
    }
    
    /**
     * @return FunctionExpression
     */
    final public static function FunctionDeclaration($Name, array $ParameterExpressions, BlockExpression $BodyExpression) {
        return new FunctionExpression($Name, $ParameterExpressions, $BodyExpression);
    }
    
    /**
     * @return GotoExpression
     */
    final public static function GotoStatement($LabelName) {
        return new GotoExpression($LabelName);
    }
    
    /**
     * @return IndexExpression
     */
    final public static function Index(ValueExpression $ValueExpression, ValueExpression $IndexValueExpression) {
        return new IndexExpression($ValueExpression, $IndexValueExpression);
    }
    
    /**
     * @return InterfaceExpression
     */
    final public static function InterfaceDeclaration($Name, 
            array $ExtendedInterfaces = array(),
            array $ConstantDeclarationExpressions = array(), 
            array $MethodSignatureExpressions = array()) {
        return new InterfaceExpression($Name, $ExtendedInterfaces, 
                $ConstantDeclarationExpressions, $MethodSignatureExpressions);
    }
    
    /**
     * @return InvocationExpression
     */
    final public static function Invoke(VariableExpression $VariableExpression, 
            array $ArgumentValueExpressions = array()) {
        return new InvocationExpression($VariableExpression, $ArgumentValueExpressions);
    }
    
    /**
     * @return IssetExpression
     */
    final public static function IssetValues(array $ValueExpressions) {
        return new IssetExpression($ValueExpressions);
    }
    
    /**
     * @return LabelExpression
     */
    final public static function Label($LabelName) {
        return new LabelExpression($LabelName);
    }
    
    /**
     * @return ListExpression
     */
    final public static function ListVariables(array $VariableExpressions) {
        return new ListExpression($VariableExpressions);
    }
    
    /**
     * @return MemberConstantExpression
     */
    final public static function MemberConstant(ValueExpression $ClassTypeValueExpression, $ConstantName) {
        return new MemberConstantExpression($ClassTypeValueExpression, $ConstantName);
    } 
    
    
    /**
     * @return MemberConstantDeclarationExpression
     */
    final public static function MemberConstantDeclaration($AccessLevel, $Name, $Value) {
        return new MemberConstantDeclarationExpression($AccessLevel, $Name, $Value);
    }
    
    /**
     * @return MethodCallExpression
     */
    final public static function MethodCall(ValueExpression $ObjectValueExpression, 
            ValueExpression $NameValueExpression, 
            array $ArgumentValueExpressions = array()) {
        return new MethodCallExpression($ObjectValueExpression, $NameValueExpression, $ArgumentValueExpressions);
    }
    
    /**
     * @return MethodExpression
     */
    final public static function MethodDeclaration(MethodSignatureExpression $SignatureExpression, 
            BlockExpression $BodyExpression = null) {
        return new MethodExpression($SignatureExpression, $BodyExpression);
    }
    
    /**
     * @return MethodSignatureExpression
     */
    final public static function MethodSignature($AccessLevel, $IsStatic, $IsFinal, $IsAbstract, 
            $Name, array $ParameterExpressions = array()) {
        return new MethodSignatureExpression($AccessLevel, $IsStatic, $IsFinal, $IsAbstract, $Name, $ParameterExpressions);
    }
    
    /**
     * @return MultipleExpression
     */
    final public static function Multiple(array $Expressions) {
        return new MultipleExpression($Expressions);
    }
    
    /**
     * @return NameExpression
     */
    final public static function Name($Name) {
        return new NameExpression($Name);
    }
    
    /**
     * @return NamedConstantExpression
     */
    final public static function NamedConstant($Name) {
        return new NamedConstantExpression($Name);
    }
    
    /**
     * @return NamespaceExpression
     */
    final public static function NamespaceDeclaration($Namespace, BlockExpression $BodyExpression = null) {
        return new NamespaceExpression($Namespace, $BodyExpression);
    }
    
    /**
     * @return NewArrayExpression
     */
    final public static function NewArray(array $KeyValueExpressions = array(), array $ValueExpressions = array()) {
        return new NewArrayExpression($KeyValueExpressions, $ValueExpressions);
    }
    
    /**
     * @return NewInstanceExpression
     */
    final public static function NewInstance(ValueExpression $ClassTypeValueExpression, array $ArgumentValueExpressions = array()) {
        return new NewInstanceExpression($ClassTypeValueExpression, $ArgumentValueExpressions);
    }
    
    /**
     * @return ParameterExpression
     */
    final public static function Parameter($Name, $TypeHint = null, $IsPassedByReference = false, 
            ValueExpression $DefaultValueExpression = null) {
        return new ParameterExpression($Name, $TypeHint, $IsPassedByReference, $DefaultValueExpression);
    }
    
    /**
     * @return PropertyDeclarationExpression
     */
    final public static function PropertyDeclaration($AccessLevel, $IsStatic, $Name, ValueExpression $ValueExpression = null) {
        return new PropertyDeclarationExpression($AccessLevel, $IsStatic, $Name, $ValueExpression);
    }
    
    /**
     * @return PropertyExpression
     */
    final public static function Property(ValueExpression $ObjectValueExpression, ValueExpression $NameValueExpression) {
        return new PropertyExpression($ObjectValueExpression, $NameValueExpression);
    }
    
    /**
     * @return ReturnExpression
     */
    final public static function ReturnStatement(ValueExpression $ValueExpression = null) {
        return new ReturnExpression($ValueExpression);
    }
    
    /**
     * @return SwitchExpression
     */
    final public static function SwitchStructure(ValueExpression $SwitchValueExpression,
            array $CaseExpressions, DefaultExpression $DefaultExpression = null) {
        return new SwitchExpression($SwitchValueExpression, $CaseExpressions, $DefaultExpression);
    }
    
    /**
     * @return StaticPropertyExpression
     */
    final public static function StaticProperty(ValueExpression $ClassTypeValueExpression, ValueExpression $PropertyNameValueExpression) {
        return new StaticPropertyExpression($ClassTypeValueExpression, $PropertyNameValueExpression);
    }
    
    /**
     * @return StaticMethodCallExpression
     */
    final public static function StaticMethodCall(ValueExpression $ClassValueExpression, 
            ValueExpression $NameValueExpression, 
            array $ArgumentValueExpressions = array()) {
        return new StaticMethodCallExpression($ClassValueExpression, 
                $NameValueExpression, $ArgumentValueExpressions);
    }
    
    /**
     * @return TernaryExpression
     */
    final public static function Ternary(ValueExpression $ConditionExpression, ValueExpression $IfTrueExpression, ValueExpression $ElseExpression) {
        return new TernaryExpression($ConditionExpression, $IfTrueExpression, $ElseExpression);
    }
    
    /**
     * @return ThrowExpression
     */
    final public static function ThrowStatement(ValueExpression $ExceptionValueExpression) {
        return new ThrowExpression($ExceptionValueExpression);
    }
    
    /**
     * @return TryExpression
     */
    final public static function TryBlock(BlockExpression $BodyExpression, array $CatchExpressions) {
        return new TryExpression($BodyExpression, $CatchExpressions);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    final public static function UnaryOperation($UnaryOperator, ValueExpression $OperandExpression) {
        return new UnaryOperationExpression($UnaryOperator, $OperandExpression);
    }
    
    /**
     * @return UnsetExpression
     */
    final public static function UnsetValues(array $ValueExpressions) {
        return new UnsetExpression($ValueExpressions);
    }
    
    /**
     * @return UseExpression
     */
    final public static function UseDeclaration($UsedName, $Alias = null) {
        return new UseExpression($UsedName, $Alias);
    }
    
    /**
     * @return UsedVariableExpression
     */
    final public static function UsedVariable($Name, $IsReference) {
        return new UsedVariableExpression($Name, $IsReference);
    }
    
    /**
     * @return ValueStatementExpression
     */
    final public static function Statement(ValueExpression $ValueExpression) {
        return new ValueStatementExpression($ValueExpression);
    }
    
    /**
     * @return GlobalVariableExpression
     */
    final public static function GlobalVariableDeclaration(array $VariableExpressions) {
        return new GlobalVariableExpression($VariableExpressions);
    }
    
    /**
     * @return StaticVariableExpression
     */
    final public static function StaticVariableDeclaration($Name, ValueExpression $ValueExpression = null) {
        return new StaticVariableExpression($Name, $ValueExpression);
    }
    
    /**
     * @return VariableExpression
     */
    final public static function Variable($Name) {
        return new VariableExpression($Name);
    }
    
    /**
     * @return VariableVariableExpression
     */
    final public static function VariableVariable(ValueExpression $VariableNameValueExpression) {
        return new VariableVariableExpression($VariableNameValueExpression);
    }
    
    /**
     * @return WhileLoopExpression
     */
    final public static function WhileLoop(ValueExpression $ConditionalExpression, BlockExpression $BodyExpression) {
        return new WhileLoopExpression($ConditionalExpression, $BodyExpression);
    }
    
    /**
     * @return ConstantExpression
     */
    final public static function Constant($Value) {
        return new ConstantExpression($Value);
    }

    /**
     * @return AssignmentExpression
     */
    final public static function Assign(ValueExpression $AssignToValueExpression, ValueExpression $AssignmentValueExpression,
            $AssignmentOperator = Operators\Assignment::Equal) {
        return new AssignmentExpression($AssignToValueExpression, $AssignmentOperator, $AssignmentValueExpression);
    }

    // </editor-fold>
}

?>