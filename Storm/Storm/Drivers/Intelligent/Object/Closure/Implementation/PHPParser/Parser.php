<?php

namespace Storm\Drivers\Intelligent\Object\Pinq\Closure\Implementation\PHPParser;

require __DIR__ . '/Library/bootstrap.php';

use \Storm\Drivers\Intelligent\Object\Closure\IParser;

class Parser implements IParser {
    private static $PHPParser;
    private $ConstantValueNodeReplacer;

    public function __construct() {
        if(self::$PHPParser === null) {
            $this->PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
        }
        
        $this->ConstantValueNodeReplacer = new \PHPParser_NodeTraverser();
        $this->ConstantValueNodeReplacer->addVisitor($this->VariableResolverVisiter);
    }
    
    public function Parse(
            $ClosureBodySource,
            Object\EntityMap $EntityMap,
            $EntityVariableName,
            $PropertyMode) {
        $Nodes = $this->PHPParser->parse('<?php ' . $ClosureBodySource . ' ?>');
        $Nodes = $this->ConstantValueNodeReplacer->traverse($Nodes);
        
        return new AST(
                $Nodes, 
                $EntityMap, 
                $EntityVariableName,
                $PropertyMode);
    }
}

?>
