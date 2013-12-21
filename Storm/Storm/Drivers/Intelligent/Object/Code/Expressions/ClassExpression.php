<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ClassExpression extends Expression {
    private $Name;
    private $HasParentClass;
    private $ParentClass;
    private $ImplementsInterfaces;
    private $ImplementedInterfaces;
    private $PropertyDeclarationExpressions;
    private $ConstantDeclarationExpressions;
    private $MethodDeclarationExpressions;
    public function __construct($Name, $ParentClass = null, array $ImplementedInterfaces = array(), 
            array $PropertyDeclarationExpressions = array(), 
            array $ConstantDeclarationExpressions = array(), 
            array $MethodDeclarationExpressions = array()) {
        $this->Name = $Name;
        $this->HasParentClass = $ParentClass !== null;
        $this->ParentClass = $ParentClass;
        $this->ImplementedInterfaces = count($ImplementedInterfaces) > 0;
        $this->ImplementedInterfaces = $ImplementedInterfaces;
        $this->PropertyDeclarationExpressions = $PropertyDeclarationExpressions;
        $this->ConstantDeclarationExpressions = $ConstantDeclarationExpressions;
        $this->MethodDeclarationExpressions = $MethodDeclarationExpressions;
    }
    
    public function GetName() {
        return $this->Name;
    }

    public function HasParentClass() {
        return $this->HasParentClass;
    }

    public function GetParentClass() {
        return $this->ParentClass;
    }

    public function ImplementsInterfaces() {
        return $this->ImplementsInterfaces;
    }

    public function GetImplementedInterfaces() {
        return $this->ImplementedInterfaces;
    }
    
    /**
     * @return PropertyDeclarationExpression[]
     */
    public function GetPropertyDeclarationExpressions() {
        return $this->PropertyDeclarationExpressions;
    }

    /**
     * @return ConstantDeclarationExpression[]
     */
    public function GetConstantDeclarationExpressions() {
        return $this->ConstantDeclarationExpressions;
    }

    /**
     * @return MethodDeclarationExpression[]
     */
    public function GetMethodDeclarationExpressions() {
        return $this->MethodDeclarationExpressions;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'class ' . $this->Name . ' ';
        if($this->HasParentClass)
            $Code .= 'extends ' . $this->ParentClass . ' ';
        
        if($this->ImplementsInterfaces) {
            $Code .= 'implements ' . implode(', ', $this->ImplementedInterfaces) . ' ';
        }
        
        $Code .= '{';
        
        foreach($this->ConstantDeclarationExpressions as $ConstantDeclarationExpression) {
            $Code .= $ConstantDeclarationExpression->Compile();
        }
        
        foreach($this->PropertyDeclarationExpressions as $PropertyDeclarationExpression) {
            $Code .= $PropertyDeclarationExpression->Compile();
        }
        
        foreach($this->MethodDeclarationExpressions as $MethodDeclarationExpression) {
            $Code .= $MethodDeclarationExpression->Compile();
        }
        
        $Code .= '}';
    }
}

?>