<?php

namespace Penumbra\Tests\Unit\Object;

use \Penumbra\Tests\PenumbraTestCase;
use Penumbra\Core\Object\RelationshipChange;

class RelationshipChangeTest extends PenumbraTestCase {
    private $PersistedRelationship;
    private $DiscardedRelationship;
    
    protected function setUp() {
        $this->PersistedRelationship = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
        $this->DiscardedRelationship = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
    }
    
    public function testSuppliedParametersAreEqualToGetterMethods() {
        
        $RelationshipChange = new RelationshipChange($this->PersistedRelationship, $this->DiscardedRelationship);
        
        $this->assertEquals($RelationshipChange->GetPersistedEntityData(), $this->PersistedRelationship);
        $this->assertEquals($RelationshipChange->GetDiscardedIdentity(), $this->DiscardedRelationship);
    }
    
    public function testOnlyHasPersistedRelationship() {
        $RelationshipChange = new RelationshipChange($this->PersistedRelationship, null);
        
        $this->assertTrue($RelationshipChange->HasPersistedEntityData());
        $this->assertFalse($RelationshipChange->HasDiscardedIdentity());
    }
    
    public function testOnlyHasDiscardedRelationship() {
        $RelationshipChange = new RelationshipChange(null, $this->DiscardedRelationship);
        
        $this->assertTrue($RelationshipChange->HasDiscardedIdentity());
        $this->assertFalse($RelationshipChange->HasPersistedEntityData());
    }
}

?>