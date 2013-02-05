<?php

namespace ErikTrapman\Bundle\WebCommandBundle\EventListener;

use ErikTrapman\Bundle\WebCommandBundle\Event\CommandRunEvent;
use ErikTrapman\Bundle\WebCommandBundle\Output\FlashOutput;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class CommandRunListener implements EventSubscriberInterface
{
    /**
     *
     * @var FlashOutput
     */
    private $output;

    /**
     *
     * @var Session
     */
    private $session;

    private $kernel;

    public function __construct($output, $session, \AppKernel $kernel)
    {
        $this->output = $output;
        $this->session = $session;
        $this->kernel = $kernel;
    }

    public static function getSubscribedEvents()
    {
        return array('eriktrapman.command.run', array('onCommandRun'));
    }

    public function onCommandRun(CommandRunEvent $event)
    {
        $command = $event->getCommand();
        $options = $event->getOptions();
        if (function_exists('proc_open')) {
            $f = new \Symfony\Component\Process\ExecutableFinder();
            $php = $f->find('php');
            $rootDir = $this->kernel->getRootDir();
            if (!empty($options)) {
                $pb = \Symfony\Component\Process\ProcessBuilder::create(array($php, $rootDir.'/console', $command->getName()));
                foreach ($options as $name => $option) {
                    if (true === $option) {
                        $pb->add($name);
                    } else {
                        $pb->add($option);
                    }
                }
            } else {
                $pb = \Symfony\Component\Process\ProcessBuilder::create(array($php, $rootDir.'/console', $command->getName()));
            }
            $p = $pb->getProcess();
            $p->run();
            $errorOuput = $p->getErrorOutput();
            $output = $p->getOutput();
            if (strlen($errorOuput)) {
                $this->output->doWrite(nl2br($errorOuput, false));
            }
            if (strlen($output)) {
                $this->output->doWrite(nl2br($output), false);
            }
        } else {
            if ($command instanceof CacheClearCommand) {
                $this->output->doWrite('The function proc_open does not exist and I have yet to find a workaround to use the native cache:clear command in web-mode.
                    Please use the cache-remove command that comes with this bundle: eriktrapman:cache:remove');
                return;
            }
            $commandTester = new CommandTester($command);
            $args = array_merge(array('command' => $command->getName()), $options);
            $commandTester->execute($args);
            $this->output->doWrite(nl2br($commandTester->getDisplay()), false);
        }
    }
}