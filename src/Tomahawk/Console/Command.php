<?php

namespace Tomahawk\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package Tomahawk\Console
 */
class Command extends BaseCommand
{
    /**
     * The input interface implementation.
     *
     * @var InputInterface
     */
    protected $input;
    /**
     * The output interface implementation.
     *
     * @var OutputInterface
     */
    protected $output;


    public function run(InputInterface $input, OutputInterface $output)
    {
        return parent::run(
            $this->input = $input,
            $this->output = $output
        );
    }

    /**
     * @return null|int - null or 0 if everything went fine, or an error code
     */
    public function handle()
    {
        throw new LogicException('You must override the handle() method in the concrete command class.');
    }


    /**
     * Call another console command.
     *
     * @param  string  $command
     * @param  array   $arguments
     * @return int
     */
    public function call($command, array $arguments = [])
    {
        $arguments['command'] = $command;
        return $this->getApplication()->find($command)->run(
            $this->createInputFromArguments($arguments), $this->output
        );
    }

    /**
     * Call another console command silently.
     *
     * @param  string  $command
     * @param  array   $arguments
     * @return int
     */
    public function callSilent($command, array $arguments = [])
    {
        $arguments['command'] = $command;
        return $this->getApplication()->find($command)->run(
            $this->createInputFromArguments($arguments), new NullOutput()
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->handle();
    }

    protected function createInputFromArguments(array $arguments)
    {
        $input = new ArgvInput($arguments);

        if ($input->hasParameterOption(['--no-interaction'], true)) {
            $input->setInteractive(false);
        }

        return $input;
    }
}