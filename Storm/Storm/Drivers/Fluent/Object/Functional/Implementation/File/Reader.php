<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\File;

use \Storm\Drivers\Fluent\Object\Functional;

class Reader implements Functional\IReader {
    
    public function GetReflecton(callable $Function) {
        return is_array($Function) ? 
                new \ReflectionMethod($Function[0], $Function[1]) : new \ReflectionFunction($Function);
    }
    
    public function Read(\ReflectionFunctionAbstract $Reflection) {
        return new Data($Reflection, 
                function (\ReflectionFunctionAbstract $Reflection, &$SourceLines, &$BodySourceLines) {
                    $SourceLines = $this->LoadSourceLines($Reflection);
                    $BodySourceLines = $this->LoadBodySourceLines($SourceLines);
                });
    }
    
    private function LoadSourceLines(\ReflectionFunctionAbstract $Reflection) {
        if(!$Reflection->isUserDefined()) {
            throw new Functional\FunctionException('Cannot parse function: Function must be user defined');
        }
        $FileName = $Reflection->getFileName();
        if(!file_exists($FileName)) {
            throw new Functional\FunctionException('Cannot parse function: Function does not belong to a valid file (cannot be eval\'d code)');
        }
        $SourceLines = array();
        $File = new \SplFileObject($Reflection->getFileName());
        $StartLine = $Reflection->getStartLine() - 2;
        $File->seek($StartLine);
        $EndLine = $Reflection->getEndLine() - 2;
        while($File->key() <= $EndLine) {
            $SourceLines[] = trim($File->fgets());
        }
        unset($File);
        
        $FirstLine =& $SourceLines[0];
        $FirstLine = substr($FirstLine, stripos($FirstLine, 'function'));
        $LastLine =& $SourceLines[count($SourceLines) - 1];
        $LastLine = substr($LastLine, 0, strrpos($LastLine, '}') + 1);
        
        return array_filter($SourceLines);
    }
    
    private function LoadBodySourceLines(array $SourceLines) {
        $BodySourceLines = array();
        $FoundStart = false;
        foreach($SourceLines as $Line) {
            if($FoundStart) {
                $BodySourceLines[] = $Line;
            }
            else {
                $Position = strpos($Line, '{');
                if($Position !== false) {
                    $BodySourceLines[] = trim(substr($Line, $Position + 1));
                    $FoundStart = true;
                }
            }
        }
        $LastLine =& $BodySourceLines[count($BodySourceLines) - 1];
        $LastLine = substr($LastLine, 0, strrpos($LastLine, '}'));
        
        
        return array_filter($BodySourceLines);
    }
}

?>
