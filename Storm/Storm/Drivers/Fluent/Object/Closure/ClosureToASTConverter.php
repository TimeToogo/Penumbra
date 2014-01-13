<?php

namespace Storm\Drivers\Fluent\Object\Closure;

use \Storm\Core\Object;

class ClosureToASTConverter {
    /**
     * @var IReader 
     */
    private $Reader;
    /**
     * @var IParser 
     */
    private $Parser;
    
    public function __construct(IReader $Reader, IParser $Parser) {
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
     * @return IAST
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
        if($ResolveVariables) {
            $AST->Resolve($ClosureData->GetUsedVariablesMap());
        }
        
        return $AST;
    }
}

?>