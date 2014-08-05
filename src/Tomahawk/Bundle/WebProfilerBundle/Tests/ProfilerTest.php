<?php

namespace Tomahawk\Bundle\WebProfilerBundle\Tests;

use Tomahawk\Test\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\Bundle\WebProfilerBundle\Profiler;

class ProfilerTest extends TestCase
{
    protected $container;

    public function testBundleEnabled()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine);

        $profiler->enable();

        $this->assertTrue($profiler->enabled());
    }

    public function testBundleDisabled()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine);

        $profiler->disable();

        $this->assertTrue($profiler->disabled());
    }

    public function testRender()
    {
        $response = new Response();

        $engine = $this->getTemplatingEngineMock();

        $engine->expects($this->once())
            ->method('render')
            ->will($this->returnValue($response));

        $profiler = new Profiler($engine);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $profiler->render());
    }

    protected function getTemplatingEngineMock()
    {
        $engine = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $engine;
    }

}