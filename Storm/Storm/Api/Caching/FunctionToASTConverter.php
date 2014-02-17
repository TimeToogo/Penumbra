<?php

namespace Storm\Api\Caching;

use \Storm\Core\Object;
use \Storm\Api\Base;
use \Storm\Drivers\Fluent\Object\Functional;
use \Storm\Utilities\Cache\ICache;

/**
 * This class allows caching the resulting AST from a given closure, reducing parsing overhead.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionToASTConverter extends Base\FunctionToASTConverter {
    private $Cache;
    
    public function __construct(
            ICache $Cache,
            Functional\IReader $Reader, 
            Functional\IParser $Parser) {
        parent::__construct($Reader, $Parser);
        
        $this->Cache = $Cache;
    }

    public function FunctionToAST(Object\IEntityMap $EntityMap, callable $Function, $ResolveVariables = true) {
        $Reflection = $this->Reader->GetReflection($Function);
        $ClosureHash = $this->FunctionHash($Reflection);
        
        $AST = null;
        if($this->Cache->Contains($ClosureHash)) {
            $AST = $this->Cache->Retrieve($ClosureHash);
        }
        
        if(!($AST instanceof Closure\IAST)) {
            $AST = parent::FunctionToAST($EntityMap, $Function, false);
            //$AST->ExpandVariables();
            $AST->Simplify();
            $this->Cache->Save('ClosureAST-' . $ClosureHash, $AST);
        }
        
        if($ResolveVariables) {
            $AST->Resolve($Reflection->getStaticVariables());
            $AST->Simplify();
        }
        
        return $AST;
    }
    
    private function FunctionHash(\ReflectionFunctionAbstract $Reflection) {
        return md5(implode(' ', [$Reflection->getFileName(), $Reflection->getStartLine(), $Reflection->getEndLine()]));
    }
}

?>