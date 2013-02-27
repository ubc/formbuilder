<?php

namespace Meot\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('form_id')
            ->add('question_id')
            ->add('order')
            ->add('forms')
            ->add('questions')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Meot\FormBundle\Entity\FormQuestion'
        ));
    }

    public function getName()
    {
        return 'meot_formbundle_formquestiontype';
    }
}
