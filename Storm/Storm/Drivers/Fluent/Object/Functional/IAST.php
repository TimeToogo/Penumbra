<?php

namespace Storm\Drivers\Fluent\Object\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

interface IAST {    
    /**
     * @return Object\IEntityMap
     */
    public function GetEntityMap();
    
    /**
     * @return boolean
     */
    public function HasEntityMap();
    /**
     * @return Object\IEntityMap
     */
    public function SetEntityMap(Object\IEntityMap $EntityMap);
    public function GetEntityVariableName();
        
    /**
     * Removes variables from the AST by expanding the node into their original expresions
     * Example:
     * $Var = 2 + 5;
     * $Foo += $Var - 3;
     * -- Becomes --
     * $Foo += (2 + 5) - 3;
     */
    public function ExpandVariables();
    
    /**
     * Simplifies the expression tree where possible
     * Example:
     * 2 + 5;
     * -- Becomes --
     * 7;
     */
    public function Simplify();
    
    public function IsResolved();
    /**
     * @return array The names of the unresolved variables
     */
    public function GetUnresolvedVariables();
    
    public function Resolve(array $VariableValueMap);
    
    public function HasReturnNode();
    /**
     * @return INode[]
     */
    public function GetReturnNodes();
    
    /**
     * @return Expression[]
     */
    public function ParseNodes(array $Nodes = null);
    
    /**
     * @return Expression
     */
    public function ParseNode(INode $Node);
}

?>
