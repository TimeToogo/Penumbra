<?php

namespace Storm\Utilities\Iterators;

class YieldIterator implements \Iterator {
    private $Current;
    private $Callback;
    private $Position = 0;
    public function __construct(callable $Callback) {
        $this->Current = $Callback();
        $this->Callback = $Callback;
    }
    
    public function current() {
        return $this->Current;
    }

    public function key() {
        return $this->Position;
    }

    public function next() {
        $this->Position++;
        $Callback = $this->Callback;
        $this->Current = $Callback();
    }

    public function rewind() {
        
    }

    public function valid() {
        return $this->Current !== null;
    }

}

?>
