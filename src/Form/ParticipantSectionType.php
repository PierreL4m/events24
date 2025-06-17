<?php

namespace App\Form;

use App\Entity\ParticipantSection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\HttpFoundation\File\File;

class ParticipantSectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
                'required' => false
                )
            )
            ->add('name',TextType::class, array(
                'label' => 'Nom'
                )
            )
            ->add('url', UrlType::class, array(
                'label' => 'Site web'
                )
            )
           ->add('logoFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => 'Logo',
                    'required' => false,                    
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ParticipantSection::class,
        ]);
    }
}
