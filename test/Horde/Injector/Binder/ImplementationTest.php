<?php

namespace Horde\Injector\Binder;
use Horde_Test_Case as TestCase;
use \Horde_Injector_DependencyFinder;
use \Horde_Injector_Binder_Implementation;

class ImplementationTest extends TestCase
{
    public function setUp(): void
    {
        $this->df = new Horde_Injector_DependencyFinder();
    }

    public function testShouldReturnBindingDetails()
    {
        $implBinder = new Horde_Injector_Binder_Implementation(
            'IMPLEMENTATION',
            $this->df
        );

        $this->assertEquals('IMPLEMENTATION', $implBinder->getImplementation());
    }

    public function testShouldCreateInstanceOfClassWithNoDependencies()
    {
        if (class_exists('Horde_Injector_Binder_ImplementationTest__NoDependencies')) {
            $implBinder = new Horde_Injector_Binder_Implementation(
                'Horde_Injector_Binder_ImplementationTest__NoDependencies',
                $this->df
            );

            $this->assertInstanceOf(
                'Horde_Injector_Binder_ImplementationTest__NoDependencies',
                $implBinder->create($this->_getInjectorNeverCallMock())
            );
        } else {
            $this->markTestSkipped('Horde_Injector_Binder_ImplementationTest__NoDependencies not available.');
        }
        
    }

    public function testShouldCreateInstanceOfClassWithTypedDependencies()
    {
        if (class_exists('Horde_Injector_Binder_ImplementationTest__TypedDependency')) {
            $implBinder = new Horde_Injector_Binder_Implementation(
                'Horde_Injector_Binder_ImplementationTest__TypedDependency',
                $this->df
            );

            $createdInstance = $implBinder->create($this->_getInjectorReturnsNoDependencyObject());

            $this->assertInstanceOf(
                'Horde_Injector_Binder_ImplementationTest__TypedDependency',
                $createdInstance
            );

            $this->assertInstanceOf(
                'Horde_Injector_Binder_ImplementationTest__NoDependencies',
                $createdInstance->dep
            );
        } else {
            $this->markTestSkipped('Horde_Injector_Binder_ImplementationTest__TypedDependency not available. ');
        }
        
    }

    public function testShouldThrowExceptionWhenTryingToCreateInstanceOfClassWithUntypedDependencies()
    {
        $this->expectException('Horde_Injector_Exception');
        
        $implBinder = new Horde_Injector_Binder_Implementation(
            'Horde_Injector_Binder_ImplementationTest__UntypedDependency',
            $this->df
        );

        $implBinder->create($this->_getInjectorNeverCallMock());
    }

    public function testShouldUseDefaultValuesFromUntypedOptionalParameters()
    {
        if (class_exists('Horde_Injector_Binder_ImplementationTest__UntypedOptionalDependency')) {
            $implBinder = new Horde_Injector_Binder_Implementation(
                'Horde_Injector_Binder_ImplementationTest__UntypedOptionalDependency',
                $this->df
            );

            $createdInstance = $implBinder->create($this->_getInjectorNeverCallMock());

            $this->assertEquals('DEPENDENCY', $createdInstance->dep);
        } else {
            $this->markTestSkipped('Horde_Injector_Binder_ImplementationTest__UntypedOptionalDependency not available. ');
        }
        
    }

    public function testShouldThrowExceptionIfRequestedClassIsNotDefined()
    {
        $this->expectException('Horde_Injector_Exception');
        
        $implBinder = new Horde_Injector_Binder_Implementation(
            'CLASS_DOES_NOT_EXIST',
            $this->df
        );

        $implBinder->create($this->_getInjectorNeverCallMock());
    }

    public function testShouldThrowExceptionIfImplementationIsAnInterface()
    {
        $this->expectException('Horde_Injector_Exception');

        $implBinder = new Horde_Injector_Binder_Implementation(
            'Horde_Injector_Binder_ImplementationTest__Interface',
            $this->df
        );

        $implBinder->create($this->_getInjectorNeverCallMock());
    }

    public function testShouldThrowExceptionIfImplementationIsAnAbstractClass()
    {
        $this->expectException('Horde_Injector_Exception');

        $implBinder = new Horde_Injector_Binder_Implementation(
            'Horde_Injector_Binder_ImplementationTest__AbstractClass',
            $this->df
        );

        $implBinder->create($this->_getInjectorNeverCallMock());
    }

    private function _getInjectorNeverCallMock()
    {
        $injector = $this->getMockBuilder('Horde_Injector')
            ->setMethods(array('getInstance'))
            ->disableOriginalConstructor()
            ->getMock();
        $injector->expects($this->never())
            ->method('getInstance');
        return $injector;
    }

    private function _getInjectorReturnsNoDependencyObject()
    {
        $injector = $this->getMockBuilder('Horde_Injector')
            ->setMethods(array('getInstance'))
            ->disableOriginalConstructor()
            ->getMock();
        $injector->expects($this->once())
            ->method('getInstance')
            ->with($this->equalTo('Horde_Injector_Binder_ImplementationTest__NoDependencies'))
            ->will($this->returnValue(new Horde_Injector_Binder_ImplementationTest__NoDependencies()));
        return $injector;
    }
}

/**
 * Used by preceeding tests!!!
 */

class Horde_Injector_Binder_ImplementationTest__NoDependencies
{
}

class Horde_Injector_Binder_ImplementationTest__TypedDependency
{
    public $dep;

    public function __construct(Horde_Injector_Binder_ImplementationTest__NoDependencies $dep)
    {
        $this->dep = $dep;
    }
}

class Horde_Injector_Binder_ImplementationTest__UntypedDependency
{
    public function __construct($dep)
    {
    }
}

class Horde_Injector_Binder_ImplementationTest__UntypedOptionalDependency
{
    public $dep;

    public function __construct($dep = 'DEPENDENCY')
    {
        $this->dep = $dep;
    }
}

interface Horde_Injector_Binder_ImplementationTest__Interface
{
}

abstract class Horde_Injector_Binder_ImplementationTest__AbstractClass
{
}

class Horde_Injector_Binder_ImplementationTest__SetterNoDependencies
{
    public $setterDep;

    public function setDependency()
    {
        $this->setterDep = 'CALLED';
    }
}

class Horde_Injector_Binder_ImplementationTest__SetterHasDependencies
{
    public $setterDep;

    public function setDependency(Horde_Injector_Binder_ImplementationTest__NoDependencies $setterDep)
    {
        $this->setterDep = $setterDep;
    }
}
