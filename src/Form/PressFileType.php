<?php

namespace App\Form;

use App\Entity\PressFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PressFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array(
                'label' => 'Fichier pdf/jpg/png. Taille max : 5Mo',
                'required' => $options['required']
            ))
            ->add('name', TextType::class, array(
                'label' => 'Nom du fichier'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PressFile::class,
            'required' => false
        ]);
    }
}
