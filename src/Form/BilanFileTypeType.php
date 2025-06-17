<?php

namespace App\Form;

use App\Entity\BilanFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilanFileTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, array('label' => 'Label (ex : Reportage(s) vidéo - Interview(s) radio) - Revue de presse'))
            ->add('name', TextType::class, array('label' => ' Nom = Audio - Video - Pdf - Iframe - Youtube ... )'))
            ->add('type', TextType::class, array('label' => 'Type (= mp3 - mp4  - pdf - youtube - iframe ...)'))
            ->add('slug', TextType::class, array('label' => 'Slug (= audio video ou pdf)'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BilanFileType::class,
        ]);
    }
}
