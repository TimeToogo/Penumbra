<?php

namespace Storm\Api\Base;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Closure;

class ClosureToASTConverter {
    /**
     * @var Closure\IReader 
     */
    private $Reader;
    /**
     * @var Closure\IParser 
     */
    private $Parser;
    
    public function __construct(Closure\IReader $Reader, Closure\IParser $Parser) {
        $this->Reader = $Reader;
        $this->Parser = $Parser;
    }

    
    final public function GetParser() {
        return $this->Reader;
    }

    final public function GetReader() {
        return $this->Parser;
    }
    
    /**
     * @return Closure\IAST
     */
    public function ClosureToAST(Object\EntityMap $EntityMap, \Closure $Closure, $ResolveVariables = true) {
        $ClosureData = $this->Reader->Read($Closure);
        $Parameters = $ClosureData->GetParameters();
        
        if(count($Parameters) !== 1) {
            throw new \Exception('Closure must contain exaclty one parameter');
        }
        
        $EntityVariableName = $Parameters[0]->name;
        $AST = $this->Parser->Parse($ClosureData->GetBodySource(), $EntityMap, $EntityVariableName);
        $AST->ExpandVariables();
        $AST->Simplify();
        if($ResolveVariables) {
            $AST->Resolve($ClosureData->GetUsedVariablesMap());
        }
        
        return $AST;
    }
}

?>