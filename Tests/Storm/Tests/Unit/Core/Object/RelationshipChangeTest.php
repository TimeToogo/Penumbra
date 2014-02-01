<?php

namespace Storm\Tests\Unit\Object;

use \Storm\Tests\StormTestCase;
use Storm\Core\Object\RelationshipChange;

class RelationshipChangeTest extends StormTestCase {
    private $PersistedRelationship;
    private $DiscardedRelationship;
    
    protected function setUp() {
        $this->PersistedRelationship = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'PersistedRelationship');
        $this->DiscardedRelationship = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'DiscardedRelationship');
    }
    
    public function testSuppliedParametersAreEqualToGetterMethods() {
        
        $RelationshipChange = new RelationshipChange($this->PersistedRelationship, $this->DiscardedRelationship);
        
        $this->assertEquals($RelationshipChange->GetPersistedRelationship(), $this->PersistedRelationship);
        $this->assertEquals($RelationshipChange->GetDiscardedRelationship(), $this->DiscardedRelationship);
    }
    
    public function testOnlyHasPersistedRelationship() {
        $RelationshipChange = new RelationshipChange($this->PersistedRelationship, null);
        
        $this->assertTrue($RelationshipChange->HasPersistedRelationship());
        $this->assertFalse($RelationshipChange->HasDiscardedRelationship());
    }
    
    public function testOnlyHasDiscardedRelationship() {
        $RelationshipChange = new RelationshipChange(null, $this->DiscardedRelationship);
        
        $this->assertTrue($RelationshipChange->HasDiscardedRelationship());
        $this->assertFalse($RelationshipChange->HasPersistedRelationship());
    }
}

?>