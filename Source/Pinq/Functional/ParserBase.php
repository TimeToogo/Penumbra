<?php

namespace Penumbra\Pinq\Functional;

abstract class ParserBase implements IParser {    
    public function GetReflection(callable $Function) {
        return is_array($Function) ? 
                new \ReflectionMethod($Function[0], $Function[1]) : new \ReflectionFunction($Function);
    }
    
    final public function Parse(\ReflectionFunctionAbstract $Reflection) {
        if(!$Reflection->isUserDefined()) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: Function is not user defined',
                    $Reflection->getName());
        }
        
        $FileName = $Reflection->getFileName();
        if(!is_readable($FileName)) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: \'%s\' is not a valid accessable file',
                    $Reflection->getName(),
                    $FileName);
        }
        
        return $this->ParseFunction($Reflection, $FileName);
    }
    
    protected abstract function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName);
}

?>