<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions\TraversalExpression;
use \Storm\Core\Object\Expressions\PropertyExpression;

class DataAttacher extends Accessor {
    private $PropertyKey;
    
    public function __construct($FieldName) {
        parent::__construct();
        $this->PropertyKey = '__' . $FieldName;
    }

    protected function Identifier(&$Identifier) {
        $Identifier .= '->' . $this->PropertyKey;
    }
    
    public function ResolveTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression) {
        if($Expression instanceof \Storm\Core\Object\Expressions\FieldExpression
                && $Expression->GetName() === $this->PropertyKey) {
            return $PropertyExpression;
        }
    }
    
    final public function GetValue($Entity) {
        return isset($Entity->{$this->PropertyKey}) ? $Entity->{$this->PropertyKey} : null;
    }

    final public function SetValue($Entity, $Value) {
        $Entity->{$this->PropertyKey} =& $Value;
    }

}

?>
