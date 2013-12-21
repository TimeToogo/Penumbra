<?php

namespace Storm\Drivers\Intelligent\Object\Pinq\Closure;

class Reader {
    private $Closure;
    private $Reflection;
    private $Parameters;
    private $UsedVariables;
    private $SourceLines;
    private $Source;
    private $BodySourceLines;
    private $BodySource;
    
    public function __construct(\Closure $Closure) {
        $this->Closure = $Closure;
        $this->Reflection = new \ReflectionFunction($Closure);
        $this->Parameters = $this->Reflection->getParameters();
        $this->UsedVariables = $this->Reflection->getStaticVariables();
        $this->SourceLines = $this->LoadSourceLines();
        $this->Source = implode('', $this->SourceLines);
        $this->BodySourceLines = $this->LoadBodySourceLines($this->SourceLines);
        $this->BodySource = implode('', $this->BodySourceLines);
    }
    
    public function GetReflection() {
        return $this->Reflection;
    }
    
    /**
     * @return \ReflectionParameter
     */
    public function GetParameters() {
        return $this->Parameters;
    }
    
    public function GetUsedVariables() {
        return $this->UsedVariables;
    }
    
    public function GetSourceLines() {
        return $this->SourceLines;
    }
    
    public function GetSource() {
        return $this->Source;
    }
    
    public function GetBodySourceLines() {
        return $this->BodySourceLines;
    }
    
    public function GetBodySource() {
        return $this->BodySource;
    }
    
    private function LoadSourceLines() {
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
