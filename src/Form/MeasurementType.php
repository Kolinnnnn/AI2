<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Measurement;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'input' => 'datetime',
                'attr' => [
                    'placeholder' => 'Enter date',
                ],
            ])

            ->add('temperature', IntegerType::class, [
                'attr' => [
                    'min' => -50,
                    'max' => 50,
                    'placeholder' => 'Enter temperature (°C)',
                ],
            ])

            ->add('humidity', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'placeholder' => 'Enter humidity (%)',
                ],
            ])

            ->add('pressure', NumberType::class, [
                'scale' => 2,
                'attr' => [
                    'placeholder' => 'Enter pressure (hPa)',
                ],
            ])

            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'city',
                'placeholder' => 'Choose a location',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}
