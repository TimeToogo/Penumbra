<?php

namespace Storm\Core\Object\Expressions\Aggregates;

use \Storm\Core\Object\Expressions\Expression;

use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\ExpressionWalker;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ImplodeExpression extends AggregateExpression {
    private $UniqueValuesOnly;
    private $Delimiter;
    private $ValueExpression;
    
    public function __construct($UniqueValuesOnly, $Delimiter, Expression $ValueExpression) {
        $this->UniqueValuesOnly = $UniqueValuesOnly;
        $this->Delimiter = $Delimiter;
        $this->ValueExpression = $ValueExpression;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $this->Update(
                $this->UniqueValuesOnly,
                $this->Delimiter,
                $Walker->Walk($this->ValueExpression));
    }
    
    
    public function UniqueValuesOnly() {
        return $this->UniqueValuesOnly;
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