<?php

namespace App\Form\Api;

use App\Entity\CandidateParticipation;
use App\Entity\Sector;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder
            // ->add('sectors', CollectionType::class, array(
            //             'entry_type' => EntityType::class,
            //             'entry_options' => array(
            //             'class' => Sector::class,),
            //             'allow_add' => true,
            //             'allow_delete' => true,
            //             'by_reference' => false
            //         )
            //     )
        
        //;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidateParticipation::class,
            'csrf_protection' => false,
        ]);
    }
}
