<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions\TraversalExpression;
use \Penumbra\Core\Object\Expressions\PropertyExpression;

class DataAttacher extends Accessor {
    private $PropertyKey;
    
    public function __construct($FieldName) {
        parent::__construct();
        $this->PropertyKey = '__' . $FieldName;
    }

    protected function Identifier(&$Identifier) {
        $Identifier .= '->' . $this->PropertyKey;
    }
    
    public function ResolveTraversalExpression(array $TraversalExpressions, PropertyExpression $PropertyExpression, &$ResolutionDepth) {
        
    }
    
    final public function GetValue($Entity) {
        return isset($Entity->{$this->PropertyKey}) ? $Entity->{$this->PropertyKey} : null;
    }

    final public function SetValue($Entity, $Value) {
        $Entity->{$this->PropertyKey} =& $Value;
    }

}

?>
