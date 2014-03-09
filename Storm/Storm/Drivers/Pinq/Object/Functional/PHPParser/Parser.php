<?php

namespace Storm\Drivers\Pinq\Object\Functional\PHPParser;

use \Storm\Core\Object;
use \Storm\Drivers\Pinq\Object\Functional\ParserBase;

class Parser extends ParserBase {
    private static $PHPParser;
    private static $ParsedFiles;

    public function __construct() {
        
    }
    
    protected function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName, $EntityVariableName) {
        if(self::$PHPParser === null) {
            self::$PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
        }
        
        $FileNodes = $this->GetFileNodes($FileName);
        $FunctionBodyNodes = $this->GetFunctionBodyNodes($FileNodes, $Reflection);
        
        return new AST($FunctionBodyNodes, $EntityVariableName);
    }
    
    private function GetFileNodes($FileName) {
        if(!isset(self::$ParsedFiles[$FileName])) {
            $FileData = file($FileName);
            self::$ParsedFiles[$FileName] = self::$PHPParser->parse($FileData);
        }
        
        return self::$ParsedFiles[$FileName];
    }
    
    private function GetFunctionBodyNodes(array $FileNodes, \ReflectionFunctionAbstract $Reflection) {
        $FunctionLocatorTraverser = new \PHPParser_NodeTraverser();
        $FunctionLocator = new Visitors\FunctionLocatorVisitor($Reflection);
        $FunctionLocatorTraverser->addVisitor($FunctionLocator);
        $FunctionLocatorTraverser->traverse($FileNodes);
        
        if(!$FunctionLocator->HasLocatedFunction()) {
            throw new \Storm\Drivers\Pinq\Object\Functional\ASTException(
                    'Could not parse function %s: The function could not be located',
                    $Reflection->getName());
        }
        
        return $FunctionLocator->GetBodyNodes();
    }
}

?>