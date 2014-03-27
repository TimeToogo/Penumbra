<?php

namespace Penumbra\Tests;

/**
 * The base class for all penumbra test cases
 */
class PenumbraTestCase extends \PHPUnit_Framework_TestCase {
    const TestNamespace = __NAMESPACE__;
    
    const RootNamespace = '\\Penumbra\\';
    
    const APINamespace = '\\Penumbra\\API\\';
    const BaseAPINamespace = '\\Penumbra\\API\\Base\\';
    
    const UtilitiesNamespace = '\\Penumbra\\Utilities\\';
    
    const CoreNamespace = '\\Penumbra\\Core\\';
    const CoreContainersNamespace = '\\Penumbra\\Core\\Containers\\';
    const CoreObjectNamespace = '\\Penumbra\\Core\\Object\\';
    const CoreRelationalNamespace = '\\Penumbra\\Core\\Relational\\';
    const CoreMappingNamespace = '\\Penumbra\\Core\\Mapping\\';
    
    const DriversNamespace = '\\Penumbra\\Drivers\\';
    const BaseDriverNamespace = '\\Penumbra\\Drivers\\Base\\';
    const PlatformsNamespace = '\\Penumbra\\Platforms\\';
    
    final protected function getMockWithoutConstructor($ClassName) {
        return $this->getMockBuilder($ClassName)
                ->disableOriginalConstructor()
                ->getMock();
    }
    final protected function getAbstractMockWithoutConstructor($ClassName) {
        return $this->getMockBuilder($ClassName)
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
    }
}

?>