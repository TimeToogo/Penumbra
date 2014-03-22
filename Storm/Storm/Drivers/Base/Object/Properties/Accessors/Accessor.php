<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions as O;

abstract class Accessor {
    private $Identifier;
    private $EntityType = null;
    
    public function __construct() {
        $this->Identifier = '$Entity';
        $this->Identifier($this->Identifier);
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }
    
    public abstract function ResolveTraversalExpression(array $TraversalExpressions, O\PropertyExpression $PropertyExpression, &$ResolutionDepth);
    
    protected abstract function Identifier(&$Identifier);
        
    public function SetEntityType($EntityType) {
        $this->EntityType = $EntityType;     
    }
    
    public abstract function GetValue($Entity);
    public abstract function SetValue($Entity, $PropertyValue);
    
    final public function Is(Accessor $OtherAccessor) {
        return $this->Identifier === $OtherAccessor->Identifier;
    }
}

?>