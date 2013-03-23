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
        $this->cc($container->getParameter('kernel.cache_dir'));
        // build up the cache again
        file_get_contents($baseUrl);
        $output->writeln('Cache removed');
    }
    
    
    /**
     * Inspired by https://gist.github.com/1942649 but this could be any recursive delete script
     * 
     * @see https://gist.github.com/1942649
     */
    private function cc($cache_dir)
    {
        if (is_dir($cache_dir)) {
            $this->rrmdir($cache_dir);
        }
    }
    
    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $o = $dir."/".$object;
                    if (filetype($o) == "dir") {
                        $this->rrmdir($dir."/".$object);
                    } else {
                        unlink($o);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
