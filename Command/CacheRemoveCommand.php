<?php

namespace ErikTrapman\Bundle\WebCommandBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheRemoveCommand extends ContainerAwareCommand
{

    /**
     * 
     */
    protected function configure()
    {
        $this
            ->setName('eriktrapman:cache:remove')
            ->setDescription("Removes the complete cache-directory and does a cache:warmup without any additional cache-warmers")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $baseUrl = $container->getParameter('erik_trapman_web_command.base_url');
        if (!$baseUrl) {
            $output->write("Please configure a base_url in your config:\n\nerik_trapman_web_command:\n&nbsp;&nbsp;base_url: %base_url%");
            return;
        }
        $fileSystem = $container->get('filesystem');
        $fileSystem->remove($container->getParameter('kernel.cache_dir'));
        // build up the cache again
        file_get_contents($baseUrl);
    }
}
