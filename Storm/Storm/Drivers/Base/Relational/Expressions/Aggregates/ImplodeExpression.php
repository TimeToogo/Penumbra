<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\Expressions\Expression;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ImplodeExpression extends AggregateExpression {
    private $Delimiter;
    private $ValueExpression;
    
    public function __construct($UniqueValuesOnly, $Delimiter, Expression $ValueExpression) {
        parent::__construct($UniqueValuesOnly);
        $this->Delimiter = $Delimiter;
        $this->ValueExpression = $ValueExpression;
    }
    
    public function GetDelimiter() {
        return $this->Delimiter;
    }
    
    public function GetValueExpression() {
        return $this->Delimiter;
    }

    public function Simplify() {
        return $this->Update(
                $this->UniqueValuesOnly, 
                $this->Delimiter, 
                $this->ValueExpression->Simplify());
    }
    
    public function Update($UniqueValuesOnly, $Delimiter, Expression $ValueExpression) {
        if($this->UniqueValuesOnly === $UniqueValuesOnly
                && $this->Delimiter === $Delimiter
                && $this->ValueExpression === $ValueExpression) {
            return $this;
        }
        
        return new self($UniqueValuesOnly, $Delimiter, $ValueExpression);
    }
}

?>