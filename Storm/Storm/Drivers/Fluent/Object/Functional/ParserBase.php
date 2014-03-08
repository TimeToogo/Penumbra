<?php

namespace Storm\Drivers\Fluent\Object\Functional;

abstract class ParserBase implements IParser {    
    public function GetReflection(callable $Function) {
        return is_array($Function) ? 
                new \ReflectionMethod($Function[0], $Function[1]) : new \ReflectionFunction($Function);
    }
    
    final public function Parse(\ReflectionFunctionAbstract $Reflection) {
        if(!$Reflection->isUserDefined()) {
            throw new \Storm\Drivers\Fluent\Object\Functional\FunctionException(
                    'Cannot parse function %s: Function is not user defined (cannot be internal)',
                    $Reflection->getName());
        }
        
        if($Reflection->getNumberOfParameters() !== 1) {
            throw new \Storm\Drivers\Fluent\Object\Functional\FunctionException(
                    'Cannot parse function %s: Function must take a single parameter as the entity.',
                    $Reflection->getName());
        }
        $EntityVariableName = $Reflection->getParameters()[0]->getName();
        
        $FileName = $Reflection->getFileName();
        if(!is_readable($FileName)) {
            throw new \Storm\Drivers\Fluent\Object\Functional\FunctionException(
                    'Cannot parse function %s: \'%s\' is not a valid file.',
                    $Reflection->getName(),
                    $FileName);
        }
        
        return $this->ParseFunction($Reflection, $FileName, $EntityVariableName);
    }
    
    protected abstract function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName, $EntityVariableName);
}

?>