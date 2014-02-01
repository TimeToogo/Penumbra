<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser;

/**
 * @method \PHPParserNode SimplifyNode(\PHPParserNode $Node) Not abstract for type hinting
 */
abstract class NodeSimplifier {
    private $NodeType;
    
    /**
     * @var \PHPParser_NodeTraverserInterface
     */
    private $SimplifierTraverser;
    
    public function __construct($NodeType) {
        $this->NodeType = $NodeType;
    }
    
    final protected function Simplify($SubNode) {
        return $this->SimplifierTraverser->traverse([$SubNode])[0];
    }
    
    final protected function IsConstant(\PHPParser_Node $Node) {
        return $Node instanceof PHPParserConstantValueNode;
    }
    
    final protected function GetValue(PHPParserConstantValueNode $Node) {
        return $Node->Value;
    }
}


?>