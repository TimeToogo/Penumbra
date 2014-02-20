<?php

namespace Storm\Api\Base;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Functional;

class FunctionToASTConverter {
    /**
     * @var Functional\IReader 
     */
    protected $Reader;
    /**
     * @var Functional\IParser 
     */
    protected $Parser;
    
    public function __construct(Functional\IReader $Reader, Functional\IParser $Parser) {
        $this->Reader = $Reader;
        $this->Parser = $Parser;
    }

    
    final public function GetParser() {
        return $this->Parser;
    }

    final public function GetReader() {
        return $this->Reader;
    }
    
    /**
     * @return Functional\IAST
     */
    public function FunctionToAST(Object\IEntityMap $EntityMap, callable $Function, $ResolveVariables = true) {
        $FunctionData = $this->Reader->Read($this->Reader->GetReflection($Function));
        $Parameters = $FunctionData->GetParameters();
        
        if(count($Parameters) !== 1) {
            throw new InvalidFunctionException($FunctionData, 'Signature must contain a single parameter');
        }
        
        $EntityVariableName = $Parameters[0]->name;
        $AST = $this->Parser->Parse($FunctionData->GetBodySource(), $EntityMap, $EntityVariableName);
        $AST->ExpandVariables();
        if($ResolveVariables) {
            $AST->Resolve($FunctionData->GetUsedVariablesMap());
            if(!$AST->IsResolved()) {
                throw new InvalidFunctionException($FunctionData, 'Contains unresolvable variables: $' . implode(', $', $AST->GetUnresolvedVariables()));
            }
        }
        $AST->Simplify();
        
        return $AST;
    }            
}

?>