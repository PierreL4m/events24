<?php

namespace App\Form;

use App\Entity\Partner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Form\FileType;
use Symfony\Component\HttpFoundation\File\File;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                    'label' => 'Nom '
                ))
            ->add('url', TextType::class, array(
                    'label' => 'Site web '
                ))
            ->add('email', EmailType::class, array(
                    'label' => 'Email de contact',
                    'required' => true
                ))
            ->add('logoFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Logo",
                    'required' => $options['required'],
                )
            )
            ->add('phone', TextType::class, array(
                    'label' => 'Téléphone ',
                    'required' => false
                ))
            ->add('baseline', TextType::class, array(
                    'label' => 'Baseline ',
                    'required' => false
                ))
            ->add('address', TextType::class, array(
                    'label' => 'Adresse ',
                    'required' => false
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
            'required' => false
        ]);
    }
}
