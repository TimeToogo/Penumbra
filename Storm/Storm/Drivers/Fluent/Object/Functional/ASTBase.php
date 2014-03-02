<?php

namespace Storm\Drivers\Fluent\Object\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\IProperty;

abstract class ASTBase implements IAST {
    /**
     * @var Object\IEntityMap|null
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
            Object\IEntityMap $EntityMap, 
            $EntityVariableName) {
        $this->Nodes = $Nodes;
        $this->EntityMap = $EntityMap;
        $this->EntityVariableName = $EntityVariableName;
    }
    
    public function __sleep() {
        return array_diff(array_keys(get_class_vars(get_class($this))), ['EntityMap']);
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
    
    final public function HasEntityMap() {
        return $this->EntityMap !== null;
    }
    
    final public function SetEntityMap(Object\IEntityMap $EntityMap) {
        $this->EntityMap = $EntityMap;
    }

    final public function GetEntityVariableName() {
        return $this->EntityVariableName;
    }

    final public function GetPropertyMode() {
        return $this->PropertyMode;
    }
    
    final public function SetPropertyMode($PropertyMode) {
        if($PropertyMode !== self::PropertiesAreGetters && 
                $PropertyMode !== self::PropertiesAreSetters &&
                $PropertyMode !== self::PropertiesAreGettersOrSetters) {
            throw new FunctionException(
                    'The supplied property mode is invalid: %s given',
                    \Storm\Core\Utilities::GetTypeOrClass($PropertyMode));
        }
        $this->PropertyMode = $PropertyMode;
    }
    
    final public function Resolve(array $VariableValueMap) {
        if(count($VariableValueMap) === 0) {
            return;
        }
        
        $this->ResolveVariables($VariableValueMap);
    }
    protected abstract function ResolveVariables(array $VariableValueMap);
        
    final public function ParseNodes(array $Nodes = null) {
        if($Nodes === null) {
            $Nodes = $this->Nodes;
        }
        return array_map([$this, 'ParseNode'], $Nodes);
    }
    
    final public function ParseNode(INode $Node) {
        if($this->PropertyMode === null) {
            throw new FunctionException(
                    'Invalid call to %s: property mode must be set',
                    __METHOD__);
        }
        if(array_search($Node, $this->Nodes, true) === false) {
            throw new FunctionException(
                    'This supplied node is not part of this AST');
        }
        
        return $this->ParseNodeAsExpression($Node);
    }
    protected abstract function ParseNodeAsExpression(INode $Node);
    
    /**
     * @return IProperty|null
     */
    final protected function GetPropertyByExpression(Object\Expressions\TraversalExpression $TraversalExpression, &$AssignmentValueExpression = null) {
        switch ($this->PropertyMode) {
            case self::PropertiesAreGetters:
                return $this->EntityMap->GetPropertyByGetter($TraversalExpression);
                
            case self::PropertiesAreSetters:
                return $this->EntityMap->GetPropertyBySetter($TraversalExpression);
                
            case self::PropertiesAreGettersOrSetters:
                return $this->EntityMap->GetPropertyByGetter($TraversalExpression) ||
                        $this->EntityMap->GetPropertyBySetter($TraversalExpression, $AssignmentValueExpression);
                
            default:
                throw new FunctionException('No');
        }
    }
}

?>
