<?php

use Tomahawk\Templating\Helper\BlocksHelper;

class BlocksHelperTest extends PHPUnit_Framework_TestCase
{
    public function testHasGetSetDefault()
    {
        $helper = new BlocksHelper();
        $helper->setDefault('foo', 'bar');

        $this->assertEquals('bar', $helper->getDefault('foo'), '->set() sets a block value');
        $this->assertEquals('bar', $helper->getDefault('bar', 'bar'), '->get() takes a default value to return if the block does not exist');

    }

    public function testHasGetSet()
    {
        $helper = new BlocksHelper();
        $helper->set('foo', 'bar');

        $this->assertEquals('bar', $helper->get('foo'), '->set() sets a block value');
        $this->assertEquals('bar', $helper->get('bar', 'bar'), '->get() takes a default value to return if the block does not exist');

    }

    public function testOutput()
    {
        $helper = new BlocksHelper();
        $helper->set('foo', 'bar');
        ob_start();
        $ret = $helper->output('foo');
        $output = ob_get_clean();
        $this->assertEquals('bar', $output, '->output() outputs the content of a block');
        $this->assertTrue($ret, '->output() returns true if the block exists');

        ob_start();
        $ret = $helper->output('bar', 'bar');
        $output = ob_get_clean();
        $this->assertEquals('bar', $output, '->output() takes a default value to return if the block does not exist');
        $this->assertTrue($ret, '->output() returns true if the block does not exist but a default value is provided');

        ob_start();
        $ret = $helper->output('bar');
        $output = ob_get_clean();
        $this->assertEquals('', $output, '->output() outputs nothing if the block does not exist');
        $this->assertFalse($ret, '->output() returns false if the block does not exist');
    }

    public function testOutputDefault()
    {
        $helper = new BlocksHelper();
        $helper->setDefault('foo', 'bar');
        ob_start();
        $ret = $helper->output('foo');
        $output = ob_get_clean();
        $this->assertEquals('bar', $output, '->output() outputs the content of a block');
        $this->assertTrue($ret, '->output() returns true if the block exists');

        ob_start();
        $ret = $helper->output('bar', 'bar');
        $output = ob_get_clean();
        $this->assertEquals('bar', $output, '->output() takes a default value to return if the block does not exist');
        $this->assertTrue($ret, '->output() returns true if the block does not exist but a default value is provided');

        ob_start();
        $ret = $helper->output('bar');
        $output = ob_get_clean();
        $this->assertEquals('', $output, '->output() outputs nothing if the block does not exist');
        $this->assertFalse($ret, '->output() returns false if the block does not exist');
    }

    public function testStartStop()
    {
        $helper = new BlocksHelper();
        $helper->start('bar');
        echo 'foo';
        $helper->stop();
        $this->assertEquals('foo', $helper->get('bar'), '->start() starts a block');
        $this->assertTrue($helper->has('bar'), '->starts() starts a block');

        $helper->start('bar');
        try {
            $helper->start('bar');
            $helper->stop();
            $this->fail('->start() throws an InvalidArgumentException if a block with the same name is already started');
        } catch (\Exception $e) {
            $helper->stop();
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->start() throws an InvalidArgumentException if a block with the same name is already started');
            $this->assertEquals('A block named "bar" is already started.', $e->getMessage(), '->start() throws an InvalidArgumentException if a block with the same name is already started');
        }

        try {
            $helper->stop();
            $this->fail('->stop() throws an LogicException if no block is started');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\LogicException', $e, '->stop() throws an LogicException if no block is started');
            $this->assertEquals('No block started.', $e->getMessage(), '->stop() throws an LogicException if no block is started');
        }
    }

    public function testStartStopDefault()
    {
        $helper = new BlocksHelper();
        $helper->startDefault('bar');
        echo 'foo';
        $helper->stopDefault();
        $this->assertEquals('foo', $helper->getDefault('bar'), '->start() starts a block');
        $this->assertTrue($helper->hasDefault('bar'), '->starts() starts a block');

        $helper->startDefault('bar');
        try {
            $helper->startDefault('bar');
            $helper->stopDefault();
            $this->fail('->start() throws an InvalidArgumentException if a block with the same name is already started');
        } catch (\Exception $e) {
            $helper->stopDefault();
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->start() throws an InvalidArgumentException if a block with the same name is already started');
            $this->assertEquals('A block named "bar" is already started.', $e->getMessage(), '->start() throws an InvalidArgumentException if a block with the same name is already started');
        }

        try {
            $helper->stopDefault();
            $this->fail('->stop() throws an LogicException if no block is started');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\LogicException', $e, '->stop() throws an LogicException if no block is started');
            $this->assertEquals('No block started.', $e->getMessage(), '->stop() throws an LogicException if no block is started');
        }
    }
}