<?php

namespace Storm\Tests\Unit\Object;

use \Storm\Tests\StormTestCase;
use \Storm\Core\Object;

class PropertyDataTest extends StormTestCase {
    
    /**
     * @var Object\PropertyData 
     */
    protected $PropertyData;
    /**
     * @var Object\IProperty 
     */
    protected $Property1;
    /**
     * @var Object\IProperty 
     */
    protected $Property2;
    
    protected function setUp() {
        $this->Property1 = $this->MakePropertyMock('1');
        $this->Property2 = $this->MakePropertyMock('2');
        
        $this->PropertyData = $this->MakePropertyDataMock([$this->Property1, $this->Property2]);
    }
    
    protected function MakePropertyDataMock(array $Properties) {
        return $this->getMockForAbstractClass(self::CoreObjectNamespace . 'PropertyData', [$Properties]);
    }
    
    final protected function MakePropertyMock($Identifier) {
        $Property = $this->getMock(self::CoreObjectNamespace . 'IProperty');
        $Property->expects($this->any())
                ->method('GetIdentifier')
                ->will($this->returnValue($Identifier));
        
        return $Property;
    }
    
    public function testAddedValuesAreSet() {
        $this->PropertyData[$this->Property1] = true;
        $this->PropertyData[$this->Property2] = true;
        
        $this->assertTrue(isset($this->PropertyData[$this->Property1]));
        $this->assertTrue(isset($this->PropertyData[$this->Property2]));
    }
    
    /**
     * @depends testAddedValuesAreSet
     */
    public function testAddedValuesAreSameAsReturnedByIndexor() {
        $Value = 'Hello';
        $OtherValue = 'World';
        
        $this->PropertyData[$this->Property1] = $Value;
        $this->PropertyData[$this->Property2] = $OtherValue;
        
        $this->assertEquals($this->PropertyData[$this->Property1], $Value);
        $this->assertEquals($this->PropertyData[$this->Property2], $OtherValue);
    }
    
    /**
     * @depends testAddedValuesAreSet
     */
    public function testUnsetValuesAreNotSet() {        
        $this->PropertyData[$this->Property1] = true;
        $this->PropertyData[$this->Property2] = true;
        
        unset($this->PropertyData[$this->Property1]);
        unset($this->PropertyData[$this->Property2]);
        
        $this->assertFalse(isset($this->PropertyData[$this->Property1]));
        $this->assertFalse(isset($this->PropertyData[$this->Property2]));
    }
    
    /**
     * @depends testAddedValuesAreSet
     */
    public function testCorrectPropertiesAreReturned() {        
        $this->PropertyData[$this->Property1] = true;
        $this->PropertyData[$this->Property2] = true;
        
        $this->assertEquals($this->PropertyData->GetProperty($this->Property1->GetIdentifier()), $this->Property1);
        $this->assertEquals($this->PropertyData->GetProperty($this->Property2->GetIdentifier()), $this->Property2);
    }
    
    /**
     * @depends testAddedValuesAreSet
     */
    public function testEquivalentPropertyDataMatches() {
        $SameValue1 = [1,2,3,4];
        $SameValue2 = "I am another value";
        $OtherPropertyData = $this->MakePropertyDataMock([$this->Property1, $this->Property2]);
        
        $this->PropertyData[$this->Property1] = $SameValue1;
        $OtherPropertyData[$this->Property1] = $SameValue1;
        $this->PropertyData[$this->Property2] = $SameValue2;
        $OtherPropertyData[$this->Property2] = $SameValue2;
        
        
        $this->assertTrue($this->PropertyData->Matches($OtherPropertyData));
        $this->assertTrue($OtherPropertyData->Matches($this->PropertyData));
    }
}

?>