<?php

namespace Meot\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text')
            ->add('response_type')
            ->add('is_public')
            ->add('is_master')
            ->add('owner')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Meot\FormBundle\Entity\Question',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'question';
    }
}