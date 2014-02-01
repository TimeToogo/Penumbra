<?php

namespace Storm\Tests;

/**
 * The base class for all storm test cases
 */
class StormTestCase extends \PHPUnit_Framework_TestCase {
    const TestNamespace = __NAMESPACE__;
    
    const RootNamespace = '\\Storm\\';
    
    const APINamespace = '\\Storm\\API\\';
    const BaseAPINamespace = '\\Storm\\API\\Base\\';
    
    const UtilitiesNamespace = '\\Storm\\Utilities\\';
    
    const CoreNamespace = '\\Storm\\Core\\';
    const CoreContainersNamespace = '\\Storm\\Core\\Containers\\';
    const CoreObjectNamespace = '\\Storm\\Core\\Object\\';
    const CoreRelationalNamespace = '\\Storm\\Core\\Relational\\';
    const CoreMappingNamespace = '\\Storm\\Core\\Mapping\\';
    
    const DriversNamespace = '\\Storm\\Drivers\\';
    const BaseDriverNamespace = '\\Storm\\Drivers\\Base\\';
    const PlatformsNamespace = '\\Storm\\Platforms\\';
    
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