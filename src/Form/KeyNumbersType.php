<?php

namespace App\Form;

use App\Entity\KeyNumbers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KeyNumbersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exposants', TextType::class, array(
                'label' => 'Nombre d\'éxposants',
                'required' => false
            ))
            ->add('offres', TextType::class, array(
                'label' => 'Nombre d\'offres',
                'required' => false
            ))
            ->add('candidats', TextType::class, array(
                'label' => 'Nombre de candidats',
                'required' => false
            ))
            ->add('entretiens', TextType::class, array(
                'label' => 'Nombre d\'entretiens',
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => KeyNumbers::class,
        ]);
    }
}