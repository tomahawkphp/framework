<?php

namespace Tomahawk\Bundle\WebProfilerBundle\Tests;

use Tomahawk\Bundle\WebProfilerBundle\Test\MockPdo;
use Tomahawk\Test\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\Bundle\WebProfilerBundle\Profiler;

class ProfilerTest extends TestCase
{
    protected $container;

    public static function setUpBeforeClass()
    {
        define('TOMAHAWKPHP_START', time());
    }

    public function testBundleEnabled()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, $this->getDatabaseManagerMock(), 'dir');

        $profiler->enable();

        $this->assertTrue($profiler->enabled());
    }

    public function testBundleDisabled()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, $this->getDatabaseManagerMock(), 'dir');

        $profiler->disable();

        $this->assertTrue($profiler->disabled());
    }

    public function testAddQueries()
    {
        $engine = $this->getTemplatingEngineMock();

        $databaseManager = $this->getDatabaseManagerMock();

        $pdoMock = new MockPdo();

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->once())
            ->method('getPdo')
            ->will($this->returnValue($pdoMock));

        $databaseManager->expects($this->once())
            ->method('connection')
            ->will($this->returnValue($connection));


        $profiler = new Profiler($engine, $databaseManager, 'dir');


        $profiler->addQueries(array(
            array(
                'bindings' => array(1),
                'query'    => 'select * from users where id = ?'
            )
        ));

        $this->assertCount(1, $profiler->getQueries());

    }

    public function testLogs()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, $this->getDatabaseManagerMock(), 'dir');

        $profiler->addLogs(array(
            'A log'
        ));

        $this->assertCount(1, $profiler->getLogs());
    }

    public function testTimers()
    {
        $engine = $this->getTemplatingEngineMock();

        $profiler = new Profiler($engine, $this->getDatabaseManagerMock(), 'dir');

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

        $profiler = new Profiler($engine, $this->getDatabaseManagerMock(), 'dir');

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

    protected function getDatabaseManagerMock()
    {
        $manager = $this->getMockBuilder('Illuminate\Database\DatabaseManager')
            ->disableOriginalConstructor()
            ->getMock();

        return $manager;
    }

}
