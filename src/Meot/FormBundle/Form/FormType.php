<?php

namespace Meot\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('header')
            ->add('footer')
            ->add('is_public')
            ->add('owner')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Meot\FormBundle\Entity\Form',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'form';
    }
}
