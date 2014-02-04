<?php

namespace Storm\Drivers\Fluent\Object\Closure\Implementation\PHPParser;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Closure\IParser;

class Parser implements IParser {
    private static $PHPParser;
    private $ConstantValueNodeReplacer;

    public function __construct() {
        if(self::$PHPParser === null) {
            self::$PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
        }
        
        $this->ConstantValueNodeReplacer = new \PHPParser_NodeTraverser();
        $this->ConstantValueNodeReplacer->addVisitor(new Visitors\ConstantValueNodeReplacerVisitor());
    }
    
    public function Parse(
            $ClosureBodySource,
            Object\IEntityMap $EntityMap,
            $EntityVariableName) {
        $Nodes = self::$PHPParser->parse('<?php ' . $ClosureBodySource . ' ?>');
        $Nodes = $this->ConstantValueNodeReplacer->traverse($Nodes);
        
        return new AST(
                $Nodes, 
                $EntityMap, 
                $EntityVariableName);
    }
}

?>