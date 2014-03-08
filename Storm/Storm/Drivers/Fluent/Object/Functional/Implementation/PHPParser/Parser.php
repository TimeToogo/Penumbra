<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Functional\ParserBase;

class Parser extends ParserBase {
    private static $PHPParser;

    public function __construct() {
        
    }
    
    protected function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName, $EntityVariableName) {
        if(self::$PHPParser === null) {
            self::$PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
        }
        
        $FileData = file($FileName);
        
        $FileNodes = self::$PHPParser->parse($FileData);
        
        $FunctionBodyNodes = $this->GetFunctionBodyNodes($FileNodes, $Reflection);
        
        return new AST($FunctionBodyNodes, $EntityVariableName);
    }
    
    private function GetFunctionBodyNodes(array $FileNodes, \ReflectionFunctionAbstract $Reflection) {
        $FunctionLocatorTraverser = new \PHPParser_NodeTraverser();
        $FunctionLocator = new Visitors\FunctionLocatorVisitor($Reflection);
        $FunctionLocatorTraverser->addVisitor($FunctionLocator);
        $FunctionLocatorTraverser->traverse($FileNodes);
        
        if(!$FunctionLocator->HasLocatedFunction()) {
            throw new \Storm\Drivers\Fluent\Object\Functional\ASTException(
                    'Could not parse function %s: The function could not be located',
                    $Reflection->getName());
        }
        
        return $FunctionLocator->GetBodyNodes();
    }
}

?>