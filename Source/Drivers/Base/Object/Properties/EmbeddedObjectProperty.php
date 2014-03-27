<?php

namespace Penumbra\Drivers\Base\Object\Properties;

use \Penumbra\Core\Object;
use \Penumbra\Core\Object\Expressions as O;

class EmbeddedObjectProperty extends DataProperty {
    const EmbeddedObjectPropertyType = __CLASS__;
    
    /**
     * @var Object\IEntityMap 
     */
    private $EmbeddedObjectMap;
    
    /**
     * @var Object\IProperty[] 
     */
    private $OriginalProperties;
    
    /**
     * @var Property[] 
     */
    private $EmbeddedProperties;
    
    /**
     * @var array<string, string>
     */
    private $PropertyIdentifierMap;
    
    public function __construct(Accessors\Accessor $Accessor, Object\IEntityMap $EmbeddedObjectMap) {
        parent::__construct($Accessor, false);
        
        $this->EmbeddedObjectMap = $EmbeddedObjectMap;
        $this->OriginalProperties = $EmbeddedObjectMap->GetProperties();
        
        foreach($this->OriginalProperties as $PropertyIdentifier => $Property) {
            if($Property instanceof self) {
                foreach($Property->GetEmbeddedProperties() as $NestedEmbeddedProperty) {
                    $TraversingProperty = $NestedEmbeddedProperty->UpdateProperty($this->Traversing($Property));
                    $this->EmbeddedProperties[$TraversingProperty->GetIdentifier()] = $TraversingProperty;
                    $this->PropertyIdentifierMap[$TraversingProperty->GetIdentifier()] = $PropertyIdentifier;
                }
            }
            else if($Property instanceof Property) {
                $TraversingProperty = $Property->UpdateProperty($this->Traversing($Property));
                $this->EmbeddedProperties[$TraversingProperty->GetIdentifier()] = $TraversingProperty;
                $this->PropertyIdentifierMap[$TraversingProperty->GetIdentifier()] = $PropertyIdentifier;
            }
        }
    }
    
    /**
     * @return Object\IEntityMap
     */
    final public function GetEmbeddedObjectMap() {
        return $this->EmbeddedObjectMap;
    }
        
    /**
     * @return Property[]
     */
    final public function GetEmbeddedProperties() {
        return $this->EmbeddedProperties;
    }
    
    final public function InitializeEmbeddedObject($Entity, Object\RevivalData $RevivalData) {
        $EmbeddedRevivalData = $this->TranslateRevivalDataToOriginalProperties($RevivalData);
        $this->Accessor->SetValue($Entity, $this->EmbeddedObjectMap->ConstructEntity($EmbeddedRevivalData));
    }
    
    private function TranslateRevivalDataToOriginalProperties(Object\RevivalData $RevivalData) {
        $EmbeddedRevivalData = [];
        foreach($RevivalData->GetData() as $PropertyIdentifier => $Value) {
            $EmbeddedRevivalData[$this->PropertyIdentifierMap[$PropertyIdentifier]] = $Value;
        }
        return $this->EmbeddedObjectMap->RevivalData($EmbeddedRevivalData);
    }
    
    private function Traversing(Property $Property) {
        return new Accessors\Traversing([$this->Accessor, $Property->GetAccessor()]);
    }
    
    final protected function ResolveExcessTraversal(O\TraversalExpression $ExcessTraversalExpression) {
        return $this->EmbeddedObjectMap->ResolveTraversalExpression($ExcessTraversalExpression);
    }
}

?>