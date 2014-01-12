<?php

namespace Storm\Api\Caching;

use \Storm\Core\Object;
use \Storm\Drivers\Intelligent\Object\Closure;
use \Storm\Utilities\Cache\ICache;

class ClosureToASTConverter extends Closure\ClosureToASTConverter {
    private $Cache;
    private $WrappedClosureToASTConverter;
    
    public function __construct(ICache $Cache, Closure\ClosureToASTConverter $WrappedClosureToASTConverter) {
        $this->Cache = $Cache;
        $this->WrappedClosureToASTConverter = $WrappedClosureToASTConverter;
    }
    
    public function ClosureToAST(Object\EntityMap $EntityMap, \Closure $Closure, $ResolveVariables = true) {
        $Reflection = new \ReflectionFunction($Closure);
        $ClosureHash = $this->ClosureHash($Reflection);
        
        $AST = null;
        if($this->Cache->Contains($ClosureHash)) {
            $AST = $this->Cache->Retrieve($ClosureHash);
        }
        
        if(!($AST instanceof Closure\IAST)) {
            $AST = $this->WrappedClosureToASTConverter->ClosureToAST($EntityMap, $Closure, false);
            $this->Cache->Save($ClosureHash, $AST);
        }
        
        if($ResolveVariables) {
            $AST->Resolve($Reflection->getStaticVariables());
        }
        
        return $AST;
    }
    
    private function ClosureHash(\ReflectionFunction $Reflection) {
        return md5(implode(' ', [$Reflection->getFileName(), $Reflection->getStartLine(), $Reflection->getEndLine()]));
    }
}

?>