<?php

namespace Storm\Pinq\Functional;

class ParameterParser {
    
    public function GetFunctionParameterNames(\ReflectionFunctionAbstract $Reflection, array $ParameterTypeHints) {
        $Parameters = $Reflection->getParameters();
                
        $ParameterTypeHints = array_fill_keys($ParameterTypeHints, null);
        $TypeHintNameMap = $ParameterTypeHints;
        
        foreach($Parameters as $Parameter) {
            $Class = $Parameter->getClass();
            if($Class !== null && isset($ParameterTypeHints[$Class->name])) {
                $TypeHintNameMap[$Class->name] = $Parameter->name;
                unset($ParameterTypeHints[$Class->name]);
            }
        }
        
        return $TypeHintNameMap;
    }
}

?>
