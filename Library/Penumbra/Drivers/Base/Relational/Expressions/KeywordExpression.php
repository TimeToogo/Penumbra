<?php

namespace Penumbra\Drivers\Base\Relational\Expressions;
 
class KeywordExpression extends LiteralExpression {
    public function __construct($Keyword) {
        parent::__construct($Keyword);
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkKeyword($this);
    }
    
    /**
     * @return self
     */
    public function Update($Keyword) {
        if($this->GetString() === $Keyword) {
            return $this;
        }
        
        return new self($Keyword);
    }
}

?>