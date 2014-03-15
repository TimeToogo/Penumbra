<?php

namespace Storm\Drivers\Base\Relational\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class AliasExpression extends Expression {
    /**
     * @var Expression
     */
    private $ValueExpression;
    
    /**
     * @var string
     */
    private $Alias;
    
    public function __construct(Expression $ValueExpression, $Alias) {
        $this->ValueExpression = $ValueExpression;
        $this->Alias = $Alias;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkAlias($this);
    }
    
    /**
     * @return Expression
     */
    public function GetValueExpression() {
        return $this->ValueExpression;
    }
    
    /**
     * @return string
     */
    public function GetAlias() {
        return $this->Alias;
    }
        
    /**
     * @return self
     */
    public function Update(Expression $ValueExpression, $Alias) {
        if($this->ValueExpression === $ValueExpression 
                && $this->Alias === $Alias) {
            return $this;
        }
        
        return new self($ValueExpression, $Alias);
    }    
    
}

?>