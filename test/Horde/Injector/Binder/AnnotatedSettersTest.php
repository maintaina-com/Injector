<?php

namespace Horde\Injector\Binder;
use Horde_Test_Case;
use \Horde_Injector_Binder;
use \Horde_Injector;
use \Horde_Injector_DependencyFinder;
use \Horde_Injector_TopLevel;
use \Horde_Injector_Binder_AnnotatedSetters;

class AnnotatedSettersTest extends Horde_Test_Case
{
    public function testShouldCallAnnotatedSetters()
    {
        $instance = new Horde_Injector_Binder_AnnotatedSettersTest__TypedSetterDependency();
        $binder = new Horde_Injector_Binder_AnnotatedSettersTest__EmptyBinder($instance);
        $df = new Horde_Injector_DependencyFinder();
        $injector = new Horde_Injector(new Horde_Injector_TopLevel());
        $annotatedSettersBinder = new Horde_Injector_Binder_AnnotatedSetters($binder, $df);

        $this->assertNull($instance->dep);
        $newInstance = $annotatedSettersBinder->create($injector);
        $this->assertInstanceOf(Horde_Injector_Binder_AnnotatedSettersTest__NoDependencies::class, $newInstance->dep);
    }
}

/**
 * Used by preceeding tests!!!
 */

class Horde_Injector_Binder_AnnotatedSettersTest__EmptyBinder implements Horde_Injector_Binder
{
    public $instance;
    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    public function create(Horde_Injector $injector)
    {
        return $this->instance;
    }

    public function equals(Horde_Injector_Binder $otherBinder)
    {
        return false;
    }
}

class Horde_Injector_Binder_AnnotatedSettersTest__NoDependencies
{
}

class Horde_Injector_Binder_AnnotatedSettersTest__TypedSetterDependency
{
    public $dep;

    /**
     * @inject
     */
    public function setDep(Horde_Injector_Binder_AnnotatedSettersTest__NoDependencies $dep)
    {
        $this->dep = $dep;
    }
}
