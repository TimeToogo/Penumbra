<?php

namespace Storm\Tests\Unit\Containers;

use \Storm\Tests\StormTestCase;
use \Storm\Core\Containers\Registrar;

class RegistrarTest extends StormTestCase {
    private $Registrar;
    private $TypedRegistrar;
    
    protected function setUp() {
        $this->Registrar = new Registrar();
        $this->TypedRegistrar = new Registrar('stdClass');
    }
    
    public function testContainsAddedValue() {
        $Value = new \stdClass();
        
        $this->Registrar->Register($Value);
        
        $this->assertContains($Value, $this->Registrar->GetRegistered());
    }
    
    public function testContainsAllAddedValues() {
        $Values = [new \stdClass(), new \stdClass(), new \stdClass()];
        
        $this->Registrar->RegisterAll($Values);
        
        foreach ($Values as $Value) {
            $this->assertContains($Value, $this->Registrar->GetRegistered());
        }
    }
    
    public function testAllowsCorrectRegisterableType() {        
        $this->TypedRegistrar->Register(new \stdClass());
    }
    
    /**
     * @expectedException \Storm\Core\StormException
     */
    public function testDisallowsIncorrectRegisterableType() {
        $this->TypedRegistrar->Register(new \SplStack());
    }
}

?>
