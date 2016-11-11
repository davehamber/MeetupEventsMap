<?php

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;


class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control input-inline datepicker col-xs-6',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy',
                    'data-date-start-date' => '0d',
                    'data-date-default-view-date' => 'today',
                ]
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control input-inline datepicker col-xs-6',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy',
                    'data-date-start-date' => '0d'
                ]
            ])
            ->add('select', SubmitType::class, ['label' => 'Select']);
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->class,
                'intention'  => 'edit',
            )
        );
    }

    public function getName()
    {
        return 'date_range';
    }
}
