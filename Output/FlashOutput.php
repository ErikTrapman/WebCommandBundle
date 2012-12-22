<?php

namespace ErikTrapman\Bundle\WebCommandBundle\Output;

use Symfony\Component\Console\Output\Output;


class FlashOutput extends Output
{
    private $session;

    public function __construct($session)
    {
        parent::__construct();
        $this->session = $session;
    }

    public function doWrite($message, $newline = true)
    {
        $message .= ( $newline ) ? "\n" : '';
        $this->session->getFlashBag()->add('notice', $message);
    }
}