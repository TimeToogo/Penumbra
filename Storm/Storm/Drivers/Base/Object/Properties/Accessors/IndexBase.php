<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;
use \Storm\Core\Object\Expressions\TraversalExpression;
use \Storm\Core\Object\Expressions\PropertyExpression;

abstract class IndexBase {
    /**
     * @var mixed
     */
    protected $Index;
    
    public function __construct($Index) {
        $this->Index = $Index;
    }
    
    /**
     * @return mixed
     */
    final public function GetIndex() {
        return $this->Index;
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= sprintf('[%s]',
                var_export($this->Index, true) );
    }
    
    public function ParseTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression) {
        if($Expression instanceof Expressions\IndexExpression 
                && $Expression->GetIndex() === $this->FieldName) {
            return $PropertyExpression;
        }
    }
    
    public function SetEntityType($EntityType) { }
}

?>
