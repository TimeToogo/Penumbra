<?php

namespace Storm\Drivers\Intelligent\Object\Closure;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

abstract class ASTBase implements IAST {
    /**
     * @var Object\EntityMap 
     */
    protected $EntityMap;
    
    protected $EntityVariableName;
    protected $PropertyMode;
    
    /**
     * @var INode[] 
     */
    protected $Nodes;
    
    public function __construct(
            array $Nodes, 
            Object\EntityMap $EntityMap, 
            $EntityVariableName) {
        $this->Nodes = $Nodes;
        $this->EntityMap = $EntityMap;
        $this->EntityVariableName = $EntityVariableName;
    }
    
    final public function GetNodes() {
        return $this->Nodes;
    }
    
    final protected function SetNodes(array $Nodes) {
        $this->Nodes = $Nodes;
    }
    
    final public function GetEntityMap() {
        return $this->EntityMap;
    }

    final public function GetEntityVariableName() {
        return $this->EntityVariableName;
    }

    final public function GetPropertyMode() {
        return $this->PropertyMode;
    }
    
    final public function SetPropertyMode($PropertyMode) {
        if($PropertyMode !== self::PropertiesAreGetters && $PropertyMode !== self::PropertiesAreSetters) {
            throw new \InvalidArgumentException('Invalid $PropertyMode supplied');
        }
        $this->PropertyMode = $PropertyMode;
    }
    
    final public function ParseNodes(array $Nodes = null) {
        if($Nodes === null) {
            $Nodes = $this->Nodes;
        }
        return array_map([$this, 'ParseNode'], $Nodes);
    }
    
    final public function ParseNode(INode $Node) {
        if($this->PropertyMode === null) {
            throw new \BadMethodCallException('PropertyMode has not been set');
        }
        if(array_search($Node, $this->Nodes, true) === false) {
            throw new \InvalidArgumentException('AST does not contain supplied node');
        }
    }
    protected abstract function ParseNodeInternal(INode $Node);
}

?>
