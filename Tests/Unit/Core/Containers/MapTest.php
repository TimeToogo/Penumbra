<?php

namespace Penumbra\Tests\Unit\Core\Containers;

use \Penumbra\Tests\PenumbraTestCase;
use \Penumbra\Core\Containers\Map;

final class MapTest extends PenumbraTestCase {
    private $Map;
    
    protected function setUp() {
        $this->Map = new Map();
    }
    
    public function testMappedObjectsIsSetInMap() {
        $Object1 = new \stdClass();
        $Object2 = new \stdClass();
        
        $this->Map[$Object1] = $Object2;
        
        $this->assertTrue(isset($this->Map[$Object1]));
        $this->assertTrue(isset($this->Map[$Object2]));
    }
    
    /**
     * @depends testMappedObjectsIsSetInMap
     */
    public function testIndexorReturnsMappedObject() {
        $Object1 = new \stdClass();
        $Object2 = new \stdClass();
        
        $this->Map[$Object1] = $Object2;
        
        $this->assertEquals($this->Map[$Object1], $Object2);
        $this->assertEquals($this->Map[$Object2], $Object1);
    }
    
    /**
     * @depends testMappedObjectsIsSetInMap
     */
    public function testUnmappedObjectAreNotSetInMap() {
        $Object1 = new \stdClass();
        $Object2 = new \stdClass();
        
        $this->Map[$Object1] = $Object2;
        
        unset($this->Map[$Object1]);
        
        $this->assertFalse(isset($this->Map[$Object1]), 'The unmapped instance is still set in the map');
        $this->assertFalse(isset($this->Map[$Object2]), 'The unmapped to instance is still set in the map');
    }
    
    public function testMappedInstanceIsReturnedFromGetInstances() {
        $Object1 = new \stdClass();
        $Object2 = new \stdClass();
        
        $this->Map[$Object1] = $Object2;
        
        $this->assertContains($Object1, $this->Map->GetInstances());
    }
    
    public function testMappedToInstanceIsReturnedFromGetToInstances() {
        $Object1 = new \stdClass();
        $Object2 = new \stdClass();
        
        $this->Map[$Object1] = $Object2;
        
        $this->assertContains($Object2, $this->Map->GetToInstances());
    }
    
    /**
     * @expectedException \Penumbra\Core\PenumbraException
     */
    public function testMapOnlyAcceptsObjects() {
        $this->Map->Map(new \stdClass(), 'Not a object!');
    }
}

?>
