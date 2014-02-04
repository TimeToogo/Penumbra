<?php

namespace Storm\Api\Caching;

use \Storm\Core\Object;
use \Storm\Api\Base;
use \Storm\Drivers\Fluent\Object\Closure;
use \Storm\Utilities\Cache\ICache;

/**
 * This class allows caching the resulting AST from a given closure, reducing parsing overhead.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ClosureToASTConverter extends Base\ClosureToASTConverter {
    private $Cache;
    
    public function __construct(
            ICache $Cache,
            Closure\IReader $Reader, 
            Closure\IParser $Parser) {
        parent::__construct($Reader, $Parser);
        
        $this->Cache = $Cache;
    }

    public function ClosureToAST(Object\EntityMap $EntityMap, \Closure $Closure, $ResolveVariables = true) {
        $Reflection = new \ReflectionFunction($Closure);
        $ClosureHash = $this->ClosureHash($Reflection);
        
        $AST = null;
        if($this->Cache->Contains($ClosureHash)) {
            $AST = $this->Cache->Retrieve($ClosureHash);
        }
        
        if(!($AST instanceof Closure\IAST)) {
            $AST = parent::ClosureToAST($EntityMap, $Closure, false);
            $AST->Simplify();
            $this->Cache->Save('ClosureAST-' . $ClosureHash, $AST);
        }
        
        if($ResolveVariables) {
            $AST->Resolve($Reflection->getStaticVariables());
            $AST->Simplify();
        }
        
        return $AST;
    }
    
    private function ClosureHash(\ReflectionFunction $Reflection) {
        return md5(implode(' ', [$Reflection->getFileName(), $Reflection->getStartLine(), $Reflection->getEndLine()]));
    }
}

?>