<?php

namespace App\Form;

use App\Entity\Bat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageLabel', TextType::class, array(
                'label' => 'Page',
                'attr' => array('placeholder' => '1-2')
            ))
            ->add('file', FileType::class, array(
                'label' => 'Fichier PDF. Taille max : 2Mo',
                'required' => $options['required']
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bat::class,
            'required' => true
        ]);
    }
}
