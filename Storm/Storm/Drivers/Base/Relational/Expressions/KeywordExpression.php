<?php

namespace Storm\Drivers\Base\Relational\Expressions;
 
class KeywordExpression extends Expression {
    private $Keyword;
    public function __construct($Keyword) {
        $this->Keyword = $Keyword;
    }
    
    public function GetKeyword() {
        return $this->Keyword;
    }
}

?>