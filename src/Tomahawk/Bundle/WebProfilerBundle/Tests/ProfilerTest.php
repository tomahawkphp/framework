<?php

namespace Tomahawk\Bundle\WebProfilerBundle\Tests;

use Doctrine\DBAL\Logging\DebugStack;
use Tomahawk\Bundle\WebProfilerBundle\Test\MockPdo;
use Tomahawk\Test\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\Bundle\WebProfilerBundle\Profiler;

class ProfilerTest extends TestCase
{
    /**
     * @var
     */
    protected $container;

    protected $startTime;
    
    public function setUp()
    {
        $this->startTime = time();
    }

    public function testBundleEnabled()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, 'dir', $this->startTime);

        $profiler->enable();

        $this->assertTrue($profiler->enabled());
    }

    public function testBundleDisabled()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, 'dir', $this->startTime);

        $profiler->disable();

        $this->assertTrue($profiler->disabled());
    }

    public function testProfilerDisabledDoesntRender()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, 'dir', $this->startTime);

        $profiler->disable();

        $this->assertEquals('', $profiler->render());
    }

    public function testAddDoctrineQueries()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, null, 'dir', $this->startTime);

        $debugStack = $this->getDebugStack();

        $profiler->addDoctrineQueries($debugStack);

        $this->assertCount(9, $profiler->getQueries());

    }

    public function testLogs()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, 'dir', $this->startTime);

        $profiler->addLogs(array(
            'A log'
        ));

        $this->assertCount(1, $profiler->getLogs());
    }

    public function testTimers()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, 'dir', $this->startTime);

        $profiler->addTimers(array(
            array(
                'name' => 'Name',
                'start' => 0,
                'end'   => 10,
                'ticks' => array(
                    array(
                        'time' => 1,
                        'diff' => 10,
                    )
                )
            )
        ));

        $this->assertCount(1, $profiler->getTimers());
    }

    public function testRender()
    {
        $response = new Response();

        $engine = $this->getTemplatingEngineMock();

        $engine->expects($this->once())
            ->method('render')
            ->will($this->returnValue($response));

        $profiler = new Profiler($engine, 'dir', $this->startTime);

        $profiler->addTimers(array(
            array(
                'name' => 'Name',
                'start' => 0,
                'end'   => 10,
                'ticks' => array(
                    array(
                        'time' => 1,
                        'diff' => 10,
                    )
                )
            )
        ));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $profiler->render());
    }

    protected function getTemplatingEngineMock()
    {
        $engine = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $engine;
    }

    protected function getDebugStack()
    {
        $debugStack = new DebugStack();

        $debugStack->queries = array(
            array(
                'sql'         => 'select * from foo where created_date = ?',
                'params'      => array(new \DateTime()),
                'types'       => array('datetime'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'select * from foo where date = ?',
                'params'      => array(new \DateTime()),
                'types'       => array('date'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'select * from foo where time = ?',
                'params'      => array(new \DateTime()),
                'types'       => array('time'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'select * from foo where tz = ?',
                'params'      => array(new \DateTimeZone('UTC')),
                'types'       => array('datetimetz'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'update foo set file = ?',
                'params'      => array('AFAKEFILEBLOB'),
                'types'       => array('object'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'update foo set data = ?',
                'params'      => array(array(1,3)),
                'types'       => array('array'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'update foo set data = ?',
                'params'      => array(array(1,3)),
                'types'       => array('simple_array'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'update foo set data = ?',
                'params'      => array(array(1,3)),
                'types'       => array('json_array'),
                'executionMS' => 0.10,
            ),
            array(
                'sql'         => 'select * from users',
                'params'      => null,
                'types'       => null,
                'executionMS' => 0.10,
            )
        );

        return $debugStack;
    }

}
