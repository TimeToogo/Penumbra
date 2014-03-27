<?php

namespace Penumbra\Tests\Unit\Object;

use \Penumbra\Tests\PenumbraTestCase;
use \Penumbra\Core\Object\IProperty;

abstract class IPropertyTest extends PenumbraTestCase {
    /**
     * @var IProperty 
     */
    protected $Property;
    
    protected abstract function GetProperty();
    
    protected function setUp() {
        $this->Property = $this->GetProperty();
    }
    
    public function testEntityMapIsSameAsSet() {
        $EntityMap = $this->getMockForAbstractClass('\Penumbra\Core\Object\EntityMap');
        
        $this->Property->SetEntityMap($EntityMap);
        
        $this->assertEquals($this->Property->GetEntityMap(), $EntityMap);
    }
}

?>