<?php

namespace App\Form;

use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Form\FileType;
use Symfony\Component\HttpFoundation\File\File;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Titre'
            ))
            ->add('menuTitle', TextType::class, array(
                'label' => 'Titre dans le menu',
                'attr' => array('placeholder' => 'Par défaut le titre'),
                'required' => false
            ))
            ->add('apiTitle', TextType::class, array(
                'label' => 'Titre sur l\'appli',
                'attr' => array('placeholder' => 'Par défaut le titre'),
                'required' => false
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
                'required' => false
            ))
            ->add('onPublic', CheckboxType::class, array(
                'label' => 'Sur le site public',
                'required' => false
            ))
            ->add('onCity', CheckboxType::class, array(
                'label' => 'Sur la page ville',
                'required' => false
            ))
            ->add('onBilan', CheckboxType::class, array(
                'label' => 'Sur le site bilan',
                'required' => false
            ))
            ->add('imageFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Image",
                    'required' => false
                )
            )
            ->add('remove_img', CheckboxType::class, array(
                    'label' => 'Supprimer l\'image',
                    'required' => false,
                    'mapped' => false
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
