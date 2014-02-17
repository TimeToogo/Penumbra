<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser;

use \Storm\Drivers\Fluent\Object\Functional\INode;
use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;

class Node implements INode {
    private $PHPParserNode;
    
    public function __construct(\PHPParser_NodeAbstract $PHPParserNode) {
        $this->PHPParserNode = $PHPParserNode;
    }
    
    /**
     * @return \PHPParser_NodeAbstract
     */
    public function GetOriginalNode() {
        return $this->PHPParserNode;
    }    
    
    public function GetType() {
        switch (true) {
            
            case $this->PHPParserNode instanceof \PHPParser_Node_Stmt:
                return self::Statement;
                
            case $this->PHPParserNode instanceof \PHPParser_Node_Expr:
                return self::Expression;
                
            default:
                return self::Other;
        }
    }
    
    public function GetSubNodes() {
        $SubNodes = $this->PHPParserNode->getIterator();
        foreach($SubNodes as &$SubNode) {
            $SubNode = new Node($SubNode);
        }
        
        return $SubNode;
    }
}

?>
