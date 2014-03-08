<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing an array declaration.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayExpression extends Expression {
    private $KeyExpressions;
    private $ValueExpressions;
    public function __construct(array $KeyExpressions, array $ValueExpressions) {
        if(array_keys($KeyExpressions) !== array_keys($ValueExpressions)) {
            throw new \Storm\Core\Object\ObjectException(
                    'The supplied key expression array keys must match the keys of the value expression array: (%s) !== (%s)',
                    implode(', ', array_keys($KeyExpressions)),
                    implode(', ', array_keys($ValueExpressions)));
        }
        $this->KeyExpressions = $KeyExpressions;
        $this->ValueExpressions = $ValueExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetKeyExpressions() {
        return $this->KeyExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetValueExpressions() {
        return $this->ValueExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkArray($this);
    }
    
    public function Simplify() {
        $KeyExpressions = self::SimplifyAll($this->KeyExpressions);
        $ValueExpressions = self::SimplifyAll($this->ValueExpressions);
        
        if(self::AllOfType($KeyExpressions, ValueExpression::GetType())
                && self::AllOfType($ValueExpressions, ValueExpression::GetType())) {
            $ResolvedArray = [];
            
            foreach($KeyExpressions as $ValueKey => $KeyExpression) {
                $ResolvedArray[$KeyExpression->GetValue()] = $ValueExpressions[$ValueKey]->GetValue();
            }
            
            return Expression::Value($ResolvedArray);
        }
        
        return $this->Update(
                $KeyExpressions, 
                $ValueExpressions);
    }
    
    /**
     * @return self
     */
    public function Update(array $KeyExpressions, array $ValueExpressions) {
        if($this->ValueExpressions === $ValueExpressions
                && $this->ValueExpressions === $KeyExpressions) {
            return $this;
        }
        
        return new self($KeyExpressions, $ValueExpressions);
    }
}

?>