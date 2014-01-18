<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object\IProperty;

/**
 * Expression an entity property.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PropertyExpression extends Expression {
    private $Property;
    public function __construct(IProperty $Property) {
        $this->Property = $Property;
    }
    
    /**
     * @return IProperty
     */
    public function GetProperty() {
        return $this->Property;
    }
}

?>