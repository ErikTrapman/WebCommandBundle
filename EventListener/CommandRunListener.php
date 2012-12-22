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

    public function __construct($output, $session)
    {
        $this->output = $output;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array('eriktrapman.command.run', array('onCommandRun'));
    }

    public function onCommandRun(CommandRunEvent $event)
    {
        $command = $event->getCommand();
        $options = $event->getOptions();

        $commandTester = new CommandTester($command);
        $args = array_merge(array('command' => $command->getName()), $options);
        // https://groups.google.com/forum/?fromgroups=#!topic/symfony2/evamavFCXic
        if ($command instanceof CacheClearCommand) {
            //$this->session->save();
            $args['--no-warmup'] = true;
        }
        $commandTester->execute($args);
        // this is actually pointless when we're executing cache:clear...
        $this->output->doWrite(nl2br($commandTester->getDisplay()), false);
    }
}