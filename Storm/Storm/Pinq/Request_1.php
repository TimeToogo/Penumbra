<?php

namespace Storm\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\IEntityMap;

class Request extends Criteria implements IQueryable  {
    private $Distinct = false;
    
    public function Distinct() {
        $this->Distinct = true;
    }

    public function Select(callable $Function) {
        
    }
    
    public function AsArray() {
        
    }

    public function getIterator() {
        return new \ArrayIterator($this->AsArray());
    }
    
    public function First() {
        $this->Limit(1);
        
    }
    
    public function All(callable $ValueFunction = null) {
        
    }

    public function Any(callable $ValueFunction = null) {
        
    }

    public function Average(callable $ValueFunction = null) {
        
    }

    public function Contains($Value) {
        
    }

    public function Count() {
        
    }

    public function GroupBy(callable $Expression) {
        
    }

    public function Having(callable $AggregateFunction) {
        
    }

    public function Implode($Delimiter, callable $ValueFunction = null) {
        
    }

    public function Maximum(callable $ValueFunction = null) {
        
    }

    public function Minimum(callable $ValueFunction = null) {
        
    }

    public function Sum(callable $ValueFunction = null) {
        
    }
}

?>
