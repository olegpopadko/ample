<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LineType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('searchString', 'text', [
                'required' => false,
            ])
            ->add('regex', 'checkbox', [
                'required' => false,
                'label'    => 'Is regex',
            ])
            ->add('file', 'entity', [
                'class'       => 'AppBundle:File',
                'required'    => false,
                'placeholder' => 'All files'
            ])
            ->add('datePeriods', 'collection', [
                'type'      => new DateRangeType(),
                'prototype' => true,
                'allow_add' => true,
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\Data\LineFilter'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_line';
    }
}
