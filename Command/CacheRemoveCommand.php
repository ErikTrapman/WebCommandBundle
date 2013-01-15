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
        // TODO bypass proc_open check -option
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
        if(function_exists('proc_open')){
            $output->write("This command is designed to run in an environment without proc_open. The command will not be executed");
            return;
        }
        
        $newCacheDir = $this->getContainer()->getParameter('kernel.cache_dir').uniqid();
        mkdir($newCacheDir);
        $warmer = $this->getContainer()->get('cache_warmer');
        //$warmer->enableOptionalWarmers();
        $warmer->warmUp($newCacheDir);
        // TODO can we find a way to keep the session-data?
        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');
        //$currentSession = serialize($_SESSION);
        //session_destroy();

        $this->cc($cacheDir);
        rename($newCacheDir, $cacheDir);
        //session_start();
        //$_SESSION = unserialize($currentSession);
        unlink($newCacheDir);
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
