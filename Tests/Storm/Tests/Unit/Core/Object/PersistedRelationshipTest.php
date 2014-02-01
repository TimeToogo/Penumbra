<?php

namespace Storm\Tests\Unit\Object;

use \Storm\Tests\StormTestCase;
use \Storm\Core\Object\PersistedRelationship;

class PersistedRelationshipTest extends StormTestCase {
    private $Identity1;
    private $Identity2;
    private $PersistenceData;
    
    protected function setUp() {
        $this->Identity1 = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
        $this->Identity2 = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'Identity');
        $this->PersistenceData = $this->getMockWithoutConstructor(self::CoreObjectNamespace . 'PersistenceData');
    }
    
    public function testSuppliedIdentitiesAreEqualToGetterMethods() {
        $PersistedRelationship = new PersistedRelationship($this->Identity1, $this->Identity2);
        
        $this->assertEquals($PersistedRelationship->GetParentIdentity(), $this->Identity1);
        $this->assertEquals($PersistedRelationship->GetRelatedIdentity(),  $this->Identity2);
    }
    
    public function testSuppliedIdentityAndPersistenceDataAreEqualToGetterMethods() {
        $PersistedRelationship = new PersistedRelationship($this->Identity1, null, $this->PersistenceData);
        
        $this->assertEquals($PersistedRelationship->GetParentIdentity(), $this->Identity1);
        $this->assertEquals($PersistedRelationship->GetChildPersistenceData(),  $this->PersistenceData);
    }
    
    public function testRelatedIdentityIsNotIndentifying() {
        $PersistedRelationship = new PersistedRelationship($this->Identity1, $this->Identity2);
        
        $this->assertFalse($PersistedRelationship->IsIdentifying());
    }
    
    public function testChildPersistenceDataIsIndentifying() {
        $PersistedRelationship = new PersistedRelationship($this->Identity1, null, $this->PersistenceData);
        
        $this->assertTrue($PersistedRelationship->IsIdentifying());
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testRelatedIdentityOrChildPersistenceDataMustBeSupplied() {
        new PersistedRelationship($this->Identity1, null, null);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testRelatedIdentityAndChildPersistenceDataCannotBothBeSupplied() {
        new PersistedRelationship($this->Identity1,$this->Identity2, $this->PersistenceData);
    }
}

?>