<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $yearsRange = range(date('Y') - 30, date('Y'));

        $builder
            ->add('startDate', 'date', [
                'years' => $yearsRange,
            ])
            ->add('endDate', 'date', [
                'years' => $yearsRange,
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\Data\DateRange'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_date_range';
    }
}
