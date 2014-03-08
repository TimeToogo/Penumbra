<?php

namespace Storm\Core\Object\Expressions\Walkers;

use \Storm\Core\Object\Expressions as O;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnresolvedValueWalker extends O\ExpressionWalker {
    private $UnresolvedVariables = [];
    private $VariableValueMap = [];
    
    public function HasUnresolvedVariables() {
        return count($this->UnresolvedVariables) > 0;
    }
    
    public function GetUnresolvedVariables() {
        return $this->UnresolvedVariables === 0;
    }
    
    public function ResetUnresolvedVariables() {
        $this->UnresolvedVariables = [];
    }
    
    public function SetVariableValueMap(array $VariableValueMap) {
        $this->VariableValueMap = $VariableValueMap;
    }
    
    public function WalkUnresolvedValue(O\UnresolvedVariableExpression $Expression) {
        $Name = $Expression->GetName();
        if(isset($this->VariableValueMap[])) {
            return O\Expression::Value($this->VariableValueMap[$Name]);
        }
        
        $this->UnresolvedVariables[] = $Expression->GetName();
        return parent::WalkUnresolvedValue($Expression);
    }
}

?>