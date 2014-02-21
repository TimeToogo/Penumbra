<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\Visitors;

use \Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\NodeSimplifier;

class SimplifierVisitor extends \PHPParser_NodeVisitorAbstract {
    
    /**
     * @var NodeSimplifier[] 
     */
    private $NodeSimplifiers = [];
    
    public function __construct(array $NodeSimplifiers) {
        foreach($NodeSimplifiers as $NodeSimplifier) {
            $this->NodeSimplifiers[$NodeSimplifier->GetNodeType()] = $NodeSimplifier;
        }
    }
    
    
    public function leaveNode(\PHPParser_Node $Node) {
        foreach($this->NodeSimplifiers as $NodeType => $NodeSimplifier) {
            if($Node instanceof $NodeType) {
                return $NodeSimplifier->Simplify($Node);
            }
        }
    }
}

?>
