<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object\IProperty;

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