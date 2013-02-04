<?php

namespace ErikTrapman\Bundle\WebCommandBundle\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\Event;

class CommandRunEvent extends Event
{
    private $command;

    private $options;

    public function __construct(Command $command, $options)
    {
        $this->command = $command;

        $executableOptions = array();
        if (strlen($options) > 0) {
            foreach (explode(" ", $options) as $option) {
                if (!$option) {
                    continue;
                }
                if (false !== strpos($option, "=")) {
                    $optionParts = explode("=",$option);
                    $executableOptions[$optionParts[0]] = $optionParts[1];
                } else {
                    $executableOptions[$option] = true;
                }
            }
        }
        $this->options = $executableOptions;
    }

    /**
     * 
     * @return \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    public function getOptions()
    {
        return $this->options;
    }
}