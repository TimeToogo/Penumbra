<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class InterfaceExpression extends Expression {
    private $Name;
    private $ExtendsInterfaces;
    private $ExtendedInterfaces;
    private $ConstantDeclarationExpressions;
    private $MethodSignatureExpressions;
    
    
    function __construct($Name, array $ExtendedInterfaces = array(),
            array $ConstantDeclarationExpressions = array(), 
            array $MethodSignatureExpressions = array()) {
        $this->Name = $Name;
        $this->ExtendsInterfaces = count($ExtendedInterfaces) > 0;
        $this->ExtendedInterfaces = $ExtendedInterfaces;
        $this->ConstantDeclarationExpressions = $ConstantDeclarationExpressions;
        $this->MethodSignatureExpressions = $MethodSignatureExpressions;
    }

    
    public function GetName() {
        return $this->Name;
    }

    public function ExtendsInterfaces() {
        return $this->ExtendsInterfaces;
    }

    public function GetExtendedInterfaces() {
        return $this->ExtendedInterfaces;
    }
    /**
     * @return MemberConstantDeclarationExpression[]
     */
    public function GetConstantDeclarationExpressions() {
        return $this->ConstantDeclarationExpressions;
    }

    /**
     * @return MethodSignatureExpression[]
     */
    public function GetMethodSignatureExpressions() {
        return $this->MethodSignatureExpressions;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= 'interface ' . $this->Name . ' ';
        if($this->ExtendsInterfaces) {
            $Code .= 'extends ';
            $Code .= implode(', ', $this->ExtendedInterfaces) . ' ';
        }
        $Code .= '{';
        
        foreach($this->ConstantDeclarationExpressions as $ConstantDeclarationExpression) {
            $Code .= $ConstantDeclarationExpression->Compile();
        }
        
        foreach($this->MethodSignatureExpressions as $MethodSignatureExpression) {
            $Code .= $MethodSignatureExpression->Compile();
        }
        
        $Code .= '}';
    }
}

?>