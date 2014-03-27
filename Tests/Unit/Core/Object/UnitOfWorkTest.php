<?php

namespace Penumbra\Tests\Unit\Object;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Tests\PenumbraTestCase;
use \Penumbra\Core\Object\Domain;
use \Penumbra\Core\Object\EntityMap;
use \Penumbra\Core\Object\IProperty;
use \Penumbra\Core\Object\UnitOfWork;

class UnitOfWorkTest extends PenumbraTestCase {
    const EntityType = 'stdClass';
    
    /**
     * @var IProperty|PHPUnit_Framework_MockObject_MockObject
     */
    private $Property;
        
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $Domain;
    
    /**
     * @var UnitOfWork
     */
    private $UnitOfWork;
    
    protected function setUp() {
        $PropertyMock = $this->getMock(self::CoreObjectNamespace . 'IDataProperty');
        
        $PropertyMock->expects($this->any())
                ->method('GetValue')
                ->will($this->returnCallback(
                        function (\stdClass $Entity) {
                            return $Entity->Id;
                        }));
                        
        $PropertyMock->expects($this->any())
                ->method('IsIdentity')
                ->will($this->returnValue(true));
        
        $EntityMapMock = $this->getAbstractMockWithoutConstructor(self::CoreObjectNamespace . 'EntityMap');
        $EntityMapMock->expects($this->any())
                ->method('EntityType')
                ->will($this->returnValue(self::EntityType));
        $EntityMapMock->__construct();
        
        $EntityMapMock->expects($this->any())
                ->method('RegisterProperties')
                ->will($this->returnCallback(
                        function (Domain $Domain, Registrar $Registrar) use($PropertyMock) {
                            $Registrar->Register($PropertyMock);
                        }));
                        
        $DomainMock = $this->getAbstractMockWithoutConstructor(self::CoreObjectNamespace . 'Domain');
        $DomainMock->expects($this->any())
                ->method('RegisterEntityMaps')
                ->will($this->returnCallback(
                        function (Registrar $Registrar) use($EntityMapMock) {
                            $Registrar->Register($EntityMapMock);
                        }));
        $DomainMock->__construct();
        
        $this->UnitOfWork = new UnitOfWork($DomainMock);
    }
    
    private function Entity($Id) {
        $Entity = new \stdClass();
        $Entity->Id = $Id;
        
        return $Entity;
    }
    
    public function testEntityIsPersisted() {
        $Entity = $this->Entity(null);
        
        $this->UnitOfWork->PersistRoot($Entity);
        
        $this->assertCount(1, $this->UnitOfWork->GetPersistenceData());
        $this->assertArrayHasKey(self::EntityType, $this->UnitOfWork->GetPersistenceDataGroups());
        $this->assertCount(1, $this->UnitOfWork->GetPersistenceDataGroups()[self::EntityType]);
    }
    
    public function testEntityWithIdentityIsDiscarded() {
        $Entity = $this->Entity(1);
        
        $this->UnitOfWork->Discard($Entity);
        
        $this->assertCount(1, $this->UnitOfWork->GetDiscardenceData());
        $this->assertArrayHasKey(self::EntityType, $this->UnitOfWork->GetDiscardenceDataGroups());
        $this->assertCount(1, $this->UnitOfWork->GetDiscardenceDataGroups()[self::EntityType]);
    }
    
    public function testEntityWithoutIdentityIsDiscarded() {
        $Entity = $this->Entity(null);
        
        $this->UnitOfWork->Discard($Entity);
        
        $this->assertCount(0, $this->UnitOfWork->GetDiscardenceData());
        $this->assertCount(0, $this->UnitOfWork->GetDiscardenceDataGroups());
    }
    
    public function testProcedureIsStoredForExecution() {
        $Procedure = $this->getMock(self::CoreObjectNamespace . 'IProcedure');
        $Procedure->expects($this->any())
                ->method('GetEntityType')
                ->will($this->returnValue(self::EntityType));
        
        $this->UnitOfWork->Execute($Procedure);
        
        $this->assertContains($Procedure, $this->UnitOfWork->GetExecutedProcedures());
    }
    
    /**
     * @expectedException \Penumbra\Core\Object\ObjectException
     */
    public function testUnmappedProcedureIsDisallowed() {
        $Procedure = $this->getMock(self::CoreObjectNamespace . 'IProcedure');
        $Procedure->expects($this->any())
                ->method('GetEntityType')
                ->will($this->returnValue(__CLASS__));
        
        $this->UnitOfWork->Execute($Procedure);
    }
    
    public function testCriteriaIsStoredForDiscarding() {
        $Criteria = $this->getMock(self::CoreObjectNamespace . 'ICriteria');
        $Criteria->expects($this->any())
                ->method('GetEntityType')
                ->will($this->returnValue(self::EntityType));
        
        $this->UnitOfWork->DiscardBy($Criteria);
        
        $this->assertContains($Criteria, $this->UnitOfWork->GetDiscardedCriteria());
    }
    
    /**
     * @expectedException \Penumbra\Core\Object\ObjectException
     */
    public function testUnmappedCriteriaIsDisallowed() {
        $Criteria = $this->getMock(self::CoreObjectNamespace . 'ICriteria');
        $Criteria->expects($this->any())
                ->method('GetEntityType')
                ->will($this->returnValue(__CLASS__));
        
        $this->UnitOfWork->DiscardBy($Criteria);
    }
}

?>