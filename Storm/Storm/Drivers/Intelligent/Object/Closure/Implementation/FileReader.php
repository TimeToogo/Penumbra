<?php

namespace Storm\Drivers\Intelligent\Object\Pinq\Closure\Implementation;

use \Storm\Drivers\Intelligent\Object\Closure\IReader;

class FileReader implements IReader {
    private $Closure;
    private $Reflection;
    private $Parameters;
    private $UsedVariablesMap;
    private $SourceLines;
    private $Source;
    private $BodySourceLines;
    private $BodySource;
    
    public function __construct(\Closure $Closure) {
        $this->Closure = $Closure;
        $this->Reflection = new \ReflectionFunction($Closure);
        $this->Parameters = $this->Reflection->getParameters();
        $this->UsedVariablesMap = $this->Reflection->getStaticVariables();
    }
    
    public function GetReflection() {
        return $this->Reflection;
    }
    
    /**
     * @return \ReflectionParameter[]
     */
    public function GetParameters() {
        return $this->Parameters;
    }
    
    public function GetUsedVariablesMap() {
        return $this->UsedVariablesMap;
    }
    
    public function GetSourceLines() {
        if($this->BodySource === null) {
            $this->LoadSource();
        }
        return $this->SourceLines;
    }
    
    public function GetSource() {
        if($this->BodySource === null) {
            $this->LoadSource();
        }
        return $this->Source;
    }
    
    public function GetBodySourceLines() {
        if($this->BodySource === null) {
            $this->LoadSource();
        }
        return $this->BodySourceLines;
    }
    
    public function GetBodySource() {
        if($this->BodySource === null) {
            $this->LoadSource();
        }
        return $this->BodySource;
    }
    
    private function LoadSource() {
        $this->SourceLines = $this->LoadSourceLines();
        $this->Source = implode('', $this->SourceLines);
        $this->BodySourceLines = $this->LoadBodySourceLines($this->SourceLines);
        $this->BodySource = implode('', $this->BodySourceLines);
    }
    
    private function LoadSourceLines() {
        $FileName = $this->Reflection->getFileName();
        if(!file_exists($FileName)) {
            throw new \Exception('Cannot parse closure: Closure does not belong to a valid file');
        }
        $SourceLines = array();
        $File = new \SplFileObject($this->Reflection->getFileName());
        $StartLine = $this->Reflection->getStartLine() - 2;
        $File->seek($StartLine);
        $EndLine = $this->Reflection->getEndLine() - 2;
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
