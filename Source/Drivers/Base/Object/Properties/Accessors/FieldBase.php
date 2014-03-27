<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions;
use \Penumbra\Core\Object\Expressions\TraversalExpression;
use \Penumbra\Core\Object\Expressions\PropertyExpression;

abstract class FieldBase extends ReflectionBase {
    protected $FieldName;
    /**
     * @var \ReflectionProperty 
     */
    protected $Reflection;
    
    public function __construct($FieldName) {
        $this->FieldName = $FieldName;
    }

    final public function GetFieldName() {
        return $this->FieldName;
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= '->' . $this->FieldName;
    }
    
    public function GetTraversalDepth() {
        return 1;
    }
    
    public function ResolveTraversalExpression(array $TraversalExpressions, PropertyExpression $PropertyExpression) {
        $Expression = $TraversalExpressions[0];
        if($Expression instanceof Expressions\FieldExpression
                && $Expression->GetNameExpression() instanceof Expressions\ValueExpression
                && $Expression->GetNameExpression()->GetValue() === $this->FieldName) {
            return $PropertyExpression;
        }
    }
        
    public function __sleep() {
        return array_merge(parent::__sleep(), ['FieldName']);
    }
    
    final protected function LoadReflection() {
        $Reflection = new \ReflectionProperty($this->EntityType, $this->FieldName);
        $Reflection->setAccessible(true);
        
        return $Reflection;
    }
}

?>