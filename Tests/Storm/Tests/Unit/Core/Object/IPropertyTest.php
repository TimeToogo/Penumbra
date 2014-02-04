<?php

namespace Storm\Tests\Unit\Object;

use \Storm\Tests\StormTestCase;
use \Storm\Core\Object\IProperty;

abstract class IPropertyTest extends StormTestCase {
    /**
     * @var IProperty 
     */
    protected $Property;
    
    protected abstract function GetProperty();
    
    protected function setUp() {
        $this->Property = $this->GetProperty();
    }
    
    public function testEntityMapIsSameAsSet() {
        $EntityMap = $this->getMockForAbstractClass('\Storm\Core\Object\EntityMap');
        
        $this->Property->SetEntityMap($EntityMap);
        
        $this->assertEquals($this->Property->GetEntityMap(), $EntityMap);
    }
}

?>