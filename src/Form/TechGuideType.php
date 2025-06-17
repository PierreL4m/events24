<?php

namespace App\Form;

use App\Entity\TechGuide;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechGuideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class,array(
                'label' => 'Fichier Pdf (Max 2Mo)',
                'required' => $options['required']
            ))
            ->add('standType', StandTypeType::class, array(
                'label' => false
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TechGuide::class,
            'required' => true
        ]);
    }
}
