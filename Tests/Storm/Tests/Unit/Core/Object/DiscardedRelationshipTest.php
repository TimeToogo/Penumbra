<?php

namespace Storm\Tests\Unit\Object;

use \Storm\Tests\StormTestCase;
use \Storm\Core\Object\DiscardedRelationship;

class DiscardedRelationshipTest extends StormTestCase {
    
    public function testSuppliedParametersAreEqualToGetterMethods() {
        $IsIdentifying = true;
        $ParentIdentity = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
        $ReletedIdentity = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
        
        $DiscardedRelationship = new DiscardedRelationship($IsIdentifying, $ParentIdentity, $ReletedIdentity);
        
        $this->assertEquals($DiscardedRelationship->IsIdentifying(), $IsIdentifying);
        $this->assertEquals($DiscardedRelationship->GetParentIdentity(), $ParentIdentity);
        $this->assertEquals($DiscardedRelationship->GetRelatedIdentity(), $ReletedIdentity);
    }
}

?>