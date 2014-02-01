<?php

namespace Storm\Tests\Unit\Object;

use \Storm\Core\Containers\Registrar;
use \Storm\Tests\StormTestCase;
use \Storm\Core\Object\Domain;
use \Storm\Core\Object\EntityMap;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Object\UnitOfWork;

class EntityMapTest extends StormTestCase {
    const EntityType = 'stdClass';
    
    /**
     * @var IDataProperty|PHPUnit_Framework_MockObject_MockObject
     */
    private $IdProperty;
    
    /**
     * @var IDataProperty|PHPUnit_Framework_MockObject_MockObject
     */
    private $DataProperty;
    
    /**
     * @var EntityMap
     */
    private $EntityMap;
    
    protected function setUp() {                        
        $this->IdProperty = $this->MakeDataPropertyMock(true, 'Id', function ($Entity) {
            return $Entity->Id;
        });
        
        $this->DataProperty = $this->MakeDataPropertyMock(false, 'Data', function ($Entity) {
            return $Entity->Data;
        });
        
        $EntityMapMock = $this->getAbstractMockWithoutConstructor(self::CoreObjectNamespace . 'EntityMap');
        $EntityMapMock->expects($this->any())
                ->method('EntityType')
                ->will($this->returnValue(self::EntityType));
        $EntityMapMock->__construct();
        
        $EntityMapMock->expects($this->any())
                ->method('RegisterProperties')
                ->will($this->returnCallback(
                        function (Domain $Domain, Registrar $Registrar) {
                            $Registrar->Register($this->IdProperty);
                            $Registrar->Register($this->DataProperty);
                        }));
                        
        $this->EntityMap = $EntityMapMock;
        $this->EntityMap->InititalizeProperties(
                $this->getAbstractMockWithoutConstructor(self::CoreObjectNamespace . 'Domain'));
    }
    
    private function Entity($Id) {
        $Entity = new \stdClass();
        $Entity->Id = $Id;
        
        return $Entity;
    }
    
    private function MakeDataPropertyMock($IsIdentity, $Identifier, callable $GetValue) {
        $PropertyMock = $this->getMock(self::CoreObjectNamespace . 'IDataProperty');
        
        $PropertyMock->expects($this->any())
                ->method('GetIdentifier')
                ->will($this->returnValue($Identifier));
                        
        $PropertyMock->expects($this->any())
                ->method('IsIdentity')
                ->will($this->returnValue($IsIdentity));
        
        $PropertyMock->expects($this->any())
                ->method('GetValue')
                ->will($this->returnCallback($GetValue));
                        
        return $PropertyMock;
    }
    
    public function testHasRegisteredProperties() {
        $Properties = $this->EntityMap->GetProperties();
        
        $this->assertContains($this->IdProperty, $Properties);
        $this->assertContains($this->DataProperty, $Properties);
    }
    
    public function testHasCorrectIdentityProperty() {
        $Properties = $this->EntityMap->GetIdentityProperties();
        
        $this->assertContains($this->IdProperty, $Properties);
        $this->assertNotContains($this->DataProperty, $Properties);
    }
    
    public function testHasCorrectEntityType() {
        $this->assertEquals(self::EntityType, $this->EntityMap->GetEntityType());
    }
    
    public function testDeterminesWhetherEntityHasAnIdentity() {
        $EntityWithIdentity = $this->Entity(5);
        $EntityWithoutIdentity = $this->Entity(null);
        
        $this->assertTrue($this->EntityMap->HasIdentity($EntityWithIdentity));
        $this->assertFalse($this->EntityMap->HasIdentity($EntityWithoutIdentity));
    }
}

?>