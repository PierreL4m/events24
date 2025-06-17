<?php

namespace App\Form;

use App\Entity\ContractType;
use App\Entity\Job;
use App\Entity\Experience;
use App\Entity\Degree;
use App\Entity\JobsList;
use App\Entity\OfferType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class JobType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $city=$city_id=null;
        $builder
            ->add('offerType', EntityType::class, array(
                'class' => OfferType::class,
                'mapped' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => 'Veuillez choisir un type d\'offre',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de choisir un type d\'offre']),
                ],
                'required' => true
            ))
            ->add('name',TextType::class, array(
                    'label' => 'Intitulé de poste *'
                )
            )
            ->add('jobType',EntityType::class, array(
                    'label' => 'Secteur d\'activité *',
                    'class' => JobsList::class,
                )
            )
            ->add('presentation', TextareaType::class, array(
                    'label' => 'Descriptif du poste/formation *',
                    'required' => false
                )
            )
            ->add('contractType',EntityType::class, array(
                    'label' => 'Type de contrat *',
                    'class' => ContractType::class,
                )
            )
            ->add('timeContract',TextType::class, array(
                    'label' => 'Durée du contrat',
                    'required' => false
                )
            )
            ->add('city',TextType::class, array(
                    'label' => 'Ville *',
                    'mapped' => false,
                    'attr' => array('list' =>'listCities' ),
                    'required' => false,
                    'constraints' => [
                        new NotNull(['message' => 'Merci de choisir une ville dans la liste.'])
                    ]
                )
            )
            ->add('city_id',HiddenType::class, array(
                    'attr' => array('list' =>'listCities' ),
                    'mapped' => false,
                    'constraints' => [
                        new NotNull(['message' => 'Merci de choisir une ville dans la liste. (merci de commencer à taper votre ville pour faire apparaître la liste)'])
                    ],
                )
            );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
            'user' => null,
            'helper' => null
        ]);
    }
}
