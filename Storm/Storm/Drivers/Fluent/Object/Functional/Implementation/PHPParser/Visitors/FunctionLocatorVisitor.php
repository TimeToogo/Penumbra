<?php

namespace Storm\Drivers\Fluent\Object\Functional\Implementation\PHPParser\Visitors;

class FunctionLocatorVisitor extends \PHPParser_NodeVisitorAbstract {
    /**
     * @var \ReflectionFunctionAbstract
     */
    private $Reflection;
    
    private $FunctionName;
    private $StartLine;
    private $EndLine;
    
    private $HasLocatedFunction = false;
    private $FunctionNode;
    private $BodyNodes;
    
    public function __construct(\ReflectionFunctionAbstract $Reflection) {
        $this->Reflection = $Reflection;
        $this->FunctionName = $Reflection->getShortName();
        $this->StartLine = $Reflection->getStartLine();
        $this->EndLine = $Reflection->getEndLine();
    }
    
    /**
     * @return boolean
     */
    public function HasLocatedFunction() {
        return $this->HasLocatedFunction;
    }
    
    /**
     * @return \PHPParser_Node
     */
    public function GetFunctionNode() {
        return $this->FunctionNode;
    }

    /**
     * @return \PHPParser_Node[]
     */
    public function GetBodyNodes() {
        return $this->BodyNodes;
    }

    public function enterNode(\PHPParser_Node $Node) {
        if($Node->getLine() === $this->StartLine && $Node->getAttribute('endLine') === $this->EndLine) {
            switch (true) {
                case $Node instanceof \PHPParser_Node_Stmt_Function && $Node->name === $this->FunctionName:
                case $Node instanceof \PHPParser_Node_Stmt_ClassMethod && $Node->name === $this->FunctionName:
                case $Node instanceof \PHPParser_Node_Expr_Closure:
                    $this->FoundFunctionNode($Node);
                    break;

                default:
                    break;
            }
        }
    }
    
    private function FoundFunctionNode(\PHPParser_Node $Node) {
        if($this->HasLocatedFunction) {
            throw new \Storm\Drivers\Fluent\Object\Functional\FunctionException(
                    'Cannot parse function defined in %s on line %d: Two anonymous functions are defined on the same line, ambiguous parse request, get your shit together',
                    $this->Reflection->getFileName(),
                    $this->StartLine);
        }
        
        $this->FunctionNode = $Node;
        $this->BodyNodes = $Node->stmts;
        $this->HasLocatedFunction = true;
    }
}

?>
