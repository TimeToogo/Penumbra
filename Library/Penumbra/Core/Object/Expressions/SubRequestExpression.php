<?php

namespace Penumbra\Core\Object\Expressions;

use \Penumbra\Core\Object\IRequest;

class SubRequestExpression extends Expression {
    /**
     * @var IRequest
     */
    private $Request;
    
    public function __construct(IRequest $Request) {
        $this->Request = $Request;
    }


    public function GetRequest() {
        return $this->Request;
    }

    public function Traverse(ExpressionWalker $Walker) {
        
    }
    
    public function Simplify() {
        return $this;
    }
    
    public function Update(IRequest $Request) {
        if($this->Request === $Request) {
            return $this;
        }
        
        return new self($Request);
    }
}

?>