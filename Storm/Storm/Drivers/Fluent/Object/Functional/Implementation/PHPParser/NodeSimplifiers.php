<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser;

/**
 * @method \PHPParserNode SimplifyNode(\PHPParser_Node $Node) Not abstract for type hinting
 */
abstract class NodeSimplifier {
    private $NodeType;
    
    public function __construct($NodeType) {
        $this->NodeType = 'PHPParser_Node_' . $NodeType;
    }
    
    final public function GetNodeType() {
        return $this->NodeType;
    }

    final protected function IsConstant(\PHPParser_Node $Node) {
        return $Node instanceof PHPParserConstantValueNode;
    }
    
    final protected function AreConstant(array $Nodes) {
        foreach($Nodes as $Node) {
            if(!$this->IsConstant($Node)) {
                return false;
            }
        }
        
        return true;
    }
    
    final protected function GetValue(PHPParserConstantValueNode $Node) {
        return $Node->Value;
    }
    
    final protected function GetValues(array $Nodes) {
        return array_map(function ($Node) { return $this->GetValue($Node); }, $Nodes);
    }
    
    final protected function Constant(&$Value) {
        return new PHPParserConstantValueNode($Value);
    }
    
    public function Simplify(\PHPParser_Node $Node) {
        $SimplifiedNode = $this->SimplifyNode($Node);
        
        if($SimplifiedNode === null) {
            return $Node;
        }
        else {
            return $SimplifiedNode;
        }
    }
}

class CastSimplifier extends NodeSimplifier {
    public function __construct() {
        parent::__construct('Expr_Cast');
    }
    
    private static $CastTypeMap = [
        'Cast_Array' => 'array',
        'Cast_Bool' => 'bool',
        'Cast_Double' => 'double',
        'Cast_Int' => 'int',
        'Cast_String' => 'string',
        'Cast_Object' => 'object',
    ];
    protected function SimplifyNode(\PHPParser_Node_Expr_Cast $Node) {
        $CastType = $Node->getType();
        if($this->IsConstant($Node->expr) && isset(self::$CastTypeMap[$CastType])) {
            $Value = $this->GetValue($Node->expr);
            
            settype($Value, self::$CastTypeMap[$CastType]);
            
            return $this->Constant($Value);
        }
    }
}

class ArrayDimFetchSimplifier extends NodeSimplifier {
    public function __construct() {
        parent::__construct('Expr_ArrayDimFetch');
    }
    
    
    protected function SimplifyNode(\PHPParser_Node_Expr_ArrayDimFetch $Node) {
        if($this->IsConstant($Node->var) && ($Node->dim === null || $this->IsConstant($Node->dim))) {
            $Array = $this->GetValue($Node->var);
            
            if($Node->dim === null) {
                $Value =& $Array[];
            }
            else {
                $Value =& $Array[$this->GetValue($Node->dim)];
            }
            
            return $this->Constant($Value);
        }
    }
}

class UnaryOperationSimplifier extends NodeSimplifier {
    private $UnaryOperationNodeType;
    private $OperandProperty;
    private $UnaryOperation;
    
    public function __construct($UnaryOperationNodeType, $OperandProperty = 'expr') {
        parent::__construct('Expr_' . $UnaryOperationNodeType);
        
        $this->UnaryOperationNodeType = $UnaryOperationNodeType;
        $this->OperandProperty = $OperandProperty;
        $this->UnaryOperation = self::UnaryOperations()[$UnaryOperationNodeType];
    }
    
    public static function All() {
        return [
            new self('BitwiseNot'),
            new self('BooleanNot'),
            new self('PostInc', 'var'),
            new self('PostDec', 'var'),
            new self('PreInc', 'var'),
            new self('PreDec', 'var'),
            new self('UnaryMinus'),
        ];
    }
    
    private static $UnaryOperations;
    private static function UnaryOperations() {
        if(self::$UnaryOperations === null) {
            self::$UnaryOperations = [
                'BitwiseNot' => function (&$I) { $I = ~$I; },
                'BooleanNot' => function (&$I) { $I = !$I; },
                'PostInc' => function (&$I) { $I = $I++; },
                'PostDec' => function (&$I) { $I = $I--; },
                'PreInc' => function (&$I) { $I = ++$I; },
                'PreDec' => function (&$I) { $I = --$I; },
                'UnaryMinus' => function (&$I) { $I = -$I; },
            ];
        }
        
        return self::$UnaryOperations;
    }
    
    
    protected function SimplifyNode(\PHPParser_Node_Expr $Node) {
        if($this->IsConstant($Node->{$this->OperandProperty})) {
            $OperandValue = $this->GetValue($Node->{$this->OperandProperty});
            
            $UnaryOperation = $this->UnaryOperation;
            $UnaryOperation($OperandValue);
            
            return $this->Constant($OperandValue);
        }
    }
}

class BinaryOperationSimplifier extends NodeSimplifier {
    private $BinaryOperationNodeType;
    private $BinaryOperation;
    
    public function __construct($BinaryOperationNodeType) {
        parent::__construct('Expr_' . $BinaryOperationNodeType);
        
        $this->BinaryOperationNodeType = $BinaryOperationNodeType;
        $this->BinaryOperation = self::BinaryOperations()[$BinaryOperationNodeType];
    }
    
    public static function All() {
        $Simplifiers = [];
        foreach(array_keys(self::BinaryOperations()) as $BinaryOperationType) {
            $Simplifiers[] = new self($BinaryOperationType);
        }
        
        return $Simplifiers;
    }
    
    private static $BinaryOperations;
    private static function BinaryOperations() {
        if(self::$BinaryOperations === null) {
            self::$BinaryOperations = [
                'BitwiseAnd' => function ($L, $R) { return $L & $R; },
                'BitwiseOr' => function ($L, $R) { return $L | $R; },
                'BitwiseXor' => function ($L, $R) { return $L ^ $R; },
                'ShiftLeft' => function ($L, $R) { return $L << $R; },
                'ShiftRight' => function ($L, $R) { return $L >> $R; },
                'BooleanAnd' => function ($L, $R) { return $L && $R; },
                'BooleanOr' => function ($L, $R) { return $L || $R; },
                'LogicalAnd' => function ($L, $R) { return $L AND $R; },
                'LogicalOr' => function ($L, $R) { return $L OR $R; },
                'Plus' => function ($L, $R) { return $L + $R; },
                'Minus' => function ($L, $R) { return $L - $R; },
                'Mul' => function ($L, $R) { return $L * $R; },
                'Div' => function ($L, $R) { return $L / $R; },
                'Mod' => function ($L, $R) { return $L % $R; },
                'Concat' => function ($L, $R) { return $L . $R; },
                'Instanceof' => function ($L, $R) { return $L instanceof $R; },
                'Equal' => function ($L, $R) { return $L == $R; },
                'Identical' => function ($L, $R) { return $L === $R; },
                'NotEqual' => function ($L, $R) { return $L != $R; },
                'NotIdentical' => function ($L, $R) { return $L !== $R; },
                'Smaller' => function ($L, $R) { return $L < $R; },
                'SmallerOrEqual' => function ($L, $R) { return $L <= $R; },
                'Greater' => function ($L, $R) { return $L > $R; },
                'GreaterOrEqual' => function ($L, $R) { return $L >= $R; },
            ];
        }
        
        return self::$BinaryOperations;
    }
    
    protected function SimplifyNode(\PHPParser_Node_Expr $Node) {
        if($this->IsConstant($Node->left) && $this->IsConstant($Node->right)) {
            $Left = $this->GetValue($Node->left);
            $Right = $this->GetValue($Node->right);
            
            $BinaryOperation = $this->BinaryOperation;
            $Value = $BinaryOperation($Left, $Right);
            
            return $this->Constant($Value);
        }
    }
}

//PHP 5.4 disallows call time pass-by-ref, Yay
class ArgSimplifier extends NodeSimplifier {
    public function __construct() {
        parent::__construct('Arg');
    }
    
    public function SimplifyNode(\PHPParser_Node_Arg $Node) {
        return $Node->value;
    }
}

class FunctionCallSimplifier extends NodeSimplifier {
    public function __construct() {
        parent::__construct('Expr_FuncCall');
    }
    
    private static $UndeterministicFunctions = [
        'rand', 
        'lcg_value',
        'srand',
        'mt_rand',
        'mt_srand',
        'array_rand',
        'openssl_random_pseudo_bytes', 
        'mcrypt_create_iv', 
        'shuffle', 
        'str_shuffle',
        'date',
        'getdate',
        'gettimeofday',
        'gmdate',
        'gmmktime',
        'localtime',
        'microtime',
        'mktime',
        'time',
    ];
    
    private function IsUndeterministicFunction($Name) {
        return in_array($Name, self::$UndeterministicFunctions) 
                || (new \ReflectionFunction($Name))->isUserDefined();
    }
    
    protected function SimplifyNode(\PHPParser_Node_Expr_FuncCall $Node) {
        if($this->AreConstant($Node->args) &&
                $Node->name instanceof \PHPParser_Node_Name || $this->IsConstant($Node->name)) {
            $Name = $this->IsConstant($Node->name) ? $this->GetValue($Node->name) : (string)$Node->name;
            
            if($this->IsUndeterministicFunction($Name)) {
                return;
            }
            
            $Result = call_user_func_array($Name, $this->GetValues($Node->args));
            
            return $this->Constant($Result);
        }
    }
}

function GetNodeSimplifiers() {
    static $NodeSimplifiers = null;
    
    if($NodeSimplifiers === null) {
        $NodeSimplifiers = [
            new CastSimplifier(),
            new ArgSimplifier(),
            new FunctionCallSimplifier(),
            new ArrayDimFetchSimplifier(),
            UnaryOperationSimplifier::All(),
            BinaryOperationSimplifier::All(),
        ];
    }
    
    foreach($NodeSimplifiers as &$NodeSimplifier) {
        if(!is_array($NodeSimplifier)) {
            $NodeSimplifier = [$NodeSimplifier];
        }
    }
    
    return call_user_func_array('array_merge', $NodeSimplifiers);
}

?>