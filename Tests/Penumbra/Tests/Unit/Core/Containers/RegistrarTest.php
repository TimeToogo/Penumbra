<?php

namespace Penumbra\Tests\Unit\Containers;

use \Penumbra\Tests\PenumbraTestCase;
use \Penumbra\Core\Containers\Registrar;

class RegistrarTest extends PenumbraTestCase {
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
     * @expectedException \Penumbra\Core\PenumbraException
     */
    public function testDisallowsIncorrectRegisterableType() {
        $this->TypedRegistrar->Register(new \SplStack());
    }
}

?>
