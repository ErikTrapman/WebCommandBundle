<?php

namespace ErikTrapman\Bundle\WebCommandBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class CommandSelectorType extends AbstractType
{
    private $transformer;

    public function __construct($transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('command', 'text')->addModelTransformer($this->transformer))
            ->add('options', null, array('required' => false))
        ;
    }

    public function getName()
    {
        return 'eriktrapman_commandselectortype';
    }
}