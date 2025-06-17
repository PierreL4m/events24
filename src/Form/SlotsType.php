<?php

namespace App\Form;

use App\Entity\Slots;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginSlot', TimeType::class, array(
                    'label' => 'Début du créneau'
                )
            )
            ->add('endingSlot', TimeType::class, array(
                    'label' => 'Fin du créneau'
                )
            )
            ->add('maxCandidats', TextType::class, array(
                'label' => 'Nombre d\'inscrits max'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slots::class,
        ]);
    }
}