<?php

namespace Storm\Drivers\Base\Relational\Expressions;
 
class KeywordExpression extends LiteralExpression {
    public function __construct($Keyword) {
        parent::__construct($Keyword);
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