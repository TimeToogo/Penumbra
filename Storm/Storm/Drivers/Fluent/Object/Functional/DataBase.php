<?php

namespace Storm\Drivers\Fluent\Object\Functional;

abstract class DataBase implements IData {
    /**
     * @var \ReflectionFunctionAbstract
     */
    protected $Reflection;
    /**
     * @var \ReflectionParameter[]
     */
    protected $Parameters;
    /**
     * @var array
     */
    protected $UsedVariablesMap;
    /**
     * @var array|null
     */
    protected $SourceLines;
    /**
     * @var string|null
     */
    protected $Source;
    /**
     * @var array|null
     */
    protected $BodySourceLines;
    /**
     * @var string|null
     */
    protected $BodySource;
    /**
     * @var callable
     */
    protected $SourceLoader;
    
    public function __construct(\ReflectionFunctionAbstract $Reflection, callable $SourceLoader) {
        $this->Reflection = $Reflection;
        $this->Parameters = $this->Reflection->getParameters();
        $this->UsedVariablesMap = $this->Reflection->getStaticVariables();
        $this->SourceLoader = $SourceLoader;
    }
    
    public function GetReflection() {
        return $this->Reflection;
    }
    
    /**
     * @return \ReflectionParameter[]
     */
    final public function GetParameters() {
        return $this->Parameters;
    }
    
    final public function GetUsedVariablesMap() {
        return $this->UsedVariablesMap;
    }
    
    final public function GetSourceLines() {
        if($this->SourceLines === null) {
            $this->LoadSource();
        }
        return $this->SourceLines;
    }
    
    final public function GetSource() {
        if($this->Source === null) {
            $this->LoadSource();
        }
        return $this->Source;
    }
    
    final public function GetBodySourceLines() {
        if($this->BodySourceLines === null) {
            $this->LoadSource();
        }
        return $this->BodySourceLines;
    }
    
    final public function GetBodySource() {
        if($this->BodySource === null) {
            $this->LoadSource();
        }
        return $this->BodySource;
    }
    
    private function LoadSource() {
        $Loader = $this->SourceLoader;
        $Loader($this->Reflection, $this->SourceLines, $this->BodySourceLines);
        $this->Source = implode(PHP_EOL, $this->SourceLines);
        $this->BodySource = implode(PHP_EOL, $this->BodySourceLines);
    }
}

?>
