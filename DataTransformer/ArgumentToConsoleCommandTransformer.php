<?php

namespace ErikTrapman\Bundle\WebCommandBundle\DataTransformer;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;


class ArgumentToConsoleCommandTransformer implements DataTransformerInterface
{
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return $value;
        }
        $s = microtime(1);
        $app = new Application($this->kernel);
        foreach ($this->kernel->getBundles() as $bundle) {
            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($app);
            }
        }
        $d = microtime(1) - $s;
        $command = $app->find($value);
        if(!$command instanceof Command){
            throw new TransformationFailedException("Unable to resolve ".$value." to a Command");
        }
        return $command;
    }

    public function transform($value)
    {
        if (null === $value) {
            return $value;
        }
        return $value->getName();
    }
}
