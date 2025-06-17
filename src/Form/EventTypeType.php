<?php

namespace App\Form;

use App\Entity\EventType;
use App\Entity\OrganizationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Host;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EventTypeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullName',TextType::class, array(
                        'label' => 'Nom Complet*',
                        'attr' => array('placeholder' => '24h pour l\'emploi et la formation')
                    )
                )
                ->add('shortName',TextType::class, array(
                        'label' => 'Nom interne*',
                        'attr' => array('placeholder' => '24')
                    )
                ) ->add('analyticsId',TextType::class, array(
                        'label' => 'Id suivi analytics',
                        'attr' => array('placeholder' => ''),
                        'required' => false
                    )
                )
                ->add('mandatoryRegistration',ChoiceType::class,
                    array(
                        'choices'  => array(                            
                            'Oui' => 1,
                            'Non' => 0,
                        ),
                        'label' => 'Inscription obligatoire'
                    )
                )
                ->add('registrationType',ChoiceType::class,
                    array(
                        'choices'  => array(
                            'Standard' => EventType::REGISTRATION_TYPE_STANDARD,
                            'Etendue (avec "Poste recherché")' => EventType::REGISTRATION_TYPE_EXTENDED,
                            'Par poste(inscription à UN poste spécifique)' => EventType::REGISTRATION_TYPE_JOB,
                            'Par participation (inscription pour UN exposant spécifique)' => EventType::REGISTRATION_TYPE_PARTICIPATION
                        ),
                        'label' => "Mode d'inscription"
                    )
                )
                ->add('registrationValidation',ChoiceType::class,
                    array(
                        'choices'  => array(
                            'Automatique' => EventType::REGISTRATION_VALIDATION_AUTO,
                            'Par admins uniquement' => EventType::REGISTRATION_VALIDATION_VIEWER,
                            'Par admins et cabinets' => EventType::REGISTRATION_VALIDATION_VIEWER_RH,
                        ),
                        'label' => 'Mode de validation des inscriptions'
                    )
                )
                ->add('registrationJoblinks',ChoiceType::class,
                array(
                    'choices'  => array(
                        'Utiliser les joblinks' => EventType::REGISTRATION_USE_JOBLINKS,
                        'Ne pas utiliser les joblinks' => EventType::REGISTRATION_DONT_USE_JOBLINKS,
                    ),
                    'label' => 'Affichage des joblinks dans la liste des candidats'
                )
            )
               ->add('organizationTypes', EntityType::class,
                    array(
                        'class' => OrganizationType::class,
                        'expanded' => true,
                        'multiple' => true,
                        'by_reference' => false,
                        'label' => 'Type d\'exposants acceptés *',
                        
                    )
                )
                ->add('host', EntityType::class, array(
                    'class' => Host::class,
                    'choice_label' => function ($host) {
                        return $host->getName();
                    },
                    'label' => 'Hôte principal *',
                    )
                )
                /*
                ->add('host', EntityType::class,
                    array(
                        'class' => Host::class,
                        'expanded' => true,
                        'multiple' => false,
                        'by_reference' => false,
                        'label' => 'Hôte principal *',
                    )
                )*/
               ->add('hosts', EntityType::class,
                    array(
                        'class' => Host::class,
                        'expanded' => true,
                        'multiple' => true,
                        'by_reference' => false,
                        'label' => 'Hôtes *'
                        
                    )
                )
               ->add('headerFile', VichFileType::class, array(
                        'attr' => array('label' =>false),
                        'label' => "Bannière pour admin exposant",
                        'required' => false,
                       
                        //'by_reference' => false

                    )
                )
                ->add('recruitmentOfficeAllowed',ChoiceType::class,
                    array(
                        'choices'  => array(
                            'Oui' => 1,
                            'Non' => 0,
                        ),
                        'label' => 'Autoriser les cabinets de recrutement à intervenir sur cet événement ?'
                    )
                )
                ->add('displayParticipationContactInfo', ChoiceType::class,
                        array(
                            'choices'  => array(
                                'Oui' => 1,
                                'Non' => 0,
                            ),
                            'label' => 'Afficher les informations des exposants ?'
                        )
                )
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventType::class
        ]);
    }
}
