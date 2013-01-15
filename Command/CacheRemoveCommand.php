<?php

namespace ErikTrapman\Bundle\WebCommandBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class CacheRemoveCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{

    /**
     * 
     */
    protected function configure()
    {
        // TODO bypass proc_open check -option
        $this
            ->setName('eriktrapman:cache:remove')
            ->setDescription("Removes the complete cache-directory and preserves session-data if ")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        if(function_exists('proc_open')){
            $output->write("This command is designed to run in an environment without proc_open. The command will not be executed");
            return;
        }
        
        $newCacheDir = $this->getContainer()->getParameter('kernel.cache_dir').uniqid();
        mkdir($newCacheDir);
        $warmer = $this->getContainer()->get('cache_warmer');
        $warmer->enableOptionalWarmers();
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

    function rrmdir($dir)
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

    function cc($cache_dir)
    {
        if (is_dir($cache_dir)) {
            $this->rrmdir($cache_dir);
        }
    }
}
