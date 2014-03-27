<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions;
use \Penumbra\Core\Object\Expressions\TraversalExpression;
use \Penumbra\Core\Object\Expressions\PropertyExpression;

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
    
    public function GetTraversalDepth() {
        return 1;
    }
    
    public function ResolveTraversalExpression(array $TraversalExpressions, PropertyExpression $PropertyExpression) {
        $Expression = $TraversalExpressions[0];
        if($Expression instanceof Expressions\IndexExpression 
                && $Expression->GetIndexExpression() instanceof Expressions\ValueExpression
                && $Expression->GetIndexExpression()->GetValue() === $this->Index) {
            return $PropertyExpression;
        }
    }
    
    public function SetEntityType($EntityType) { }
}

?>
