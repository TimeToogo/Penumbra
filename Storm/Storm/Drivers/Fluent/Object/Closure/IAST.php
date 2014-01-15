<?php

namespace Storm\Drivers\Fluent\Object\Closure;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

interface IAST {
    const PropertiesAreGetters = 1;
    const PropertiesAreSetters = 2;
    
    /**
     * @return Object\EntityMap
     */
    public function GetEntityMap();
    public function GetEntityVariableName();
    public function GetPropertyMode();
    public function SetPropertyMode($PropertyMode);
    
    /**
     * @return INode[]
     */
    public function GetNodes();
    
    /**
     * Removes variables from the AST by expanding the node into their original expresions
     * Example:
     * $Var = 2 + 5;
     * $Foo += $Var - 3;
     * -- Becomes --
     * $Foo += (2 + 5) - 3;
     */
    public function ExpandVariables();
    
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
