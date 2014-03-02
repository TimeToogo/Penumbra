<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;
use \Storm\Core\Object\Expressions\TraversalExpression;

class IndexSetter extends IndexBase implements IPropertySetter {
    
    public function MatchesExpression(TraversalExpression $Expression, &$AssignmentValueExpression = null) {
        return $Expression instanceof Expressions\IndexExpression
                && $Expression->GetIndex() === $this->Index;
    }
    
    final public function SetValueTo($Entity, $Value) {
        $Entity[$this->Index] = $Value;
    }
}

?>
