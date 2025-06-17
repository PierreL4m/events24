<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\EventJobs;
use App\Entity\EventSimple;
use App\Entity\Image;
use App\Repository\EventRepository;
// TODO "super-event" use App\Entity\EventSimpleTwoDays;
use App\Entity\OrganizationType;
use App\Entity\Sector;
use App\Entity\SectorPic;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Form\FileType;
use Symfony\Component\HttpFoundation\File\File;

class EventType extends AbstractType
{

    ////////////////////////////////////////////////////////////////////
    /// WARNING all field have to be render manually in template ///////
    ////////////////////////////////////////////////////////////////////
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = $options['data'];
        $this->buildBase($builder,$event);
        $this->buildChild($builder,$event);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }

    public function buildBase($builder, Event $event)
    {
        $array_years = array(date('Y'), date('Y')+1, date('Y')-1 );

        $builder->add('online', DateTimeType::class, array(
                    'label' => 'Date de mise en ligne (fiches exposants visibles sur le site public)',
                    'widget' => 'choice',
                    'years' => $array_years,
                    'format' => 'dMy',
                    'data' => $event->getOnline() ? $event->getOnline() :(new \DateTime())->setTime("10","30"),
                    'html5' => false

                )
            )
            ->add('offline', DateTimeType::class, array(
                    'label' => 'Date de mise hors ligne (plus visible sur la page d\'accueil)',
                    'widget' => 'choice',
                    'years' => $array_years,
                    'format' => 'dMy',
                    'data' => $event->getOffline() ? $event->getOffline() :(new \DateTime())->setTime('0','0'),
                    'html5' => false

                )
            )
            // ->add('l4mRegistration', CheckboxType::class, array(
            //         'label' => 'Proposer l\'inscription sur l4m.fr',
            //         'required' => false

            //     )

            // )

            ->add('firstRecallDate', DateType::class, array(
                    'label' => 'Date de la première relance',
                    'widget' => 'choice',
                    'years' => array(date('Y'), date('Y')+1 ),
                    'format' => 'dMy',
                    'data' => $event->getFirstRecallDate() ? $event->getFirstRecallDate() : new \DateTime()
                )
            )
            ->add('secondRecallDate', DateType::class, array(
                    'label' => 'Date de la deuxième relance',
                    'widget' => 'choice',
                    'years' => array(date('Y'), date('Y')+1 ),
                    'format' => 'dMy',
                    'data' => $event->getSecondRecallDate() ? $event->getSecondRecallDate() : new \DateTime()
                )
            )

            ->add('pubFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Image pub pour l'invitation. Dimensions : 1040x659 en 300dpi. Taille Max : 600Ko (par defaut pub l4m.fr)",
                    'required' => false
                )
            )
            ->add('pubAccredFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Image pub pour l'accreditation. Dimensions : 1040x659 en 300dpi. Taille Max : 600Ko (par defaut pub l4m.fr)",
                    'required' => false
                )
            )
            ->add('bannerFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Bannière. Taille Max : 200Ko. JPG ou PNG uniquement (automatiquement générée avec l'événement de même type de l'année précédente)",
                    'required' => false
                )
            )
            ->add('socialLogoFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Logo pour partage sur réseaux sociaux. JPG UNIQUEMENT. Dimension optimales : 476×249px . Taille max : 8Mo",
                    'required' => false
                )
            )
            ->add('logoFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Logo ".$event->getType()." JPG ou PNG. Taille max 50Ko(automatiquement généré avec l'événement de même type de l'année précédente)",
                    'required' => false
                )
            )
            ->add('backBadgeFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Image de fond du badge de l'événement : Dimension optimales : 411x244 . Taille max : 8Mo",
                    'required' => false
                )
            )
            ->add('backFacebookFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "fond des visuels Facebook",
                    'required' => false
                )
            )
            ->add('backInstaFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "fond des visuels Insta",
                    'required' => false
                )
            )
            ->add('backTwitterFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "fond des visuels Twitter",
                    'required' => false
                )
            )
            ->add('backLinkedinFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "fond des visuels Linkedin",
                    'required' => false
                )
            )
            ->add('backSignatureFile', VichFileType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "visuel pour la signature de mail",
                    'required' => false
                )
            )
            ->add('organizationTypes', EntityType::class,
                array(
                    'class' => OrganizationType::class,
                    'expanded' => true,
                    'multiple' => true,
                    'by_reference' => false,
                    'label' => 'Type d\'exposants acceptés :',

                )
            )
            ->add('date', DateTimeType::class, array(
                    'label' => 'Date',
                    'widget' => 'choice',
                    'years' => $array_years,
                    'format' => 'dMy',
                    'data' => $event->getDate() ? $event->getDate() :(new \DateTime())->setTime('9','30'),
                    'html5' => false

                )
            )
            ->add('closingAt', TimeType::class, array(
                    'label' => 'Heure de fermeture',
                    'data' => $event->getClosingAt() ? $event->getClosingAt() :new \DateTime('17:30')
                )
            )
            ->add('dateGuide', DateType::class, array(
                    'label' => 'Date d\'envoi du guide à l\'impression',
                    'widget' => 'choice',
                    'years' => array(date('Y'), date('Y')+1 ),
                    'data' => new \DateTime()
                )
            )
            ->add('nbStand', IntegerType::class, array(
                    'label' => 'Nombre total de stands sur le plan'
                )
            )
            ->add('parentEvent', EntityType::class,[
                    'class' => Event::class,
                    'query_builder' => function (EventRepository $er) {
                        return $er->createQueryBuilder('e')
                            ->where('e.offline >= :now')
                            ->andWhere('e.date >= :now')
                            ->setParameter('now', date('Y-m-d H:i:s'))
                            ->orderBy('e.id', 'DESC');;
                    },
                    'label' => 'L\'événement est-il rattachéà un autre ?',
                    'required' => false
            ])
            ->add('sectorPics', EntityType::class, array(
                    'class' => SectorPic::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'Pictos Secteur présent sur le site publique',
                    'required' => false,
                    'by_reference' => false
                )
            )
        ;

    }
    public function buildEventSimple($builder, $event)
        {
            $builder->add('startBreak', TimeType::class, array(
                            'label' => 'Début de la pause',
                            'data' => $event->getStartBreak() ? $event->getStartBreak() : new \DateTime('12:30')
                        )
                    )
                    ->add('endBreak', TimeType::class, array(
                            'label' => 'Fin de la pause',
                            'data' => $event->getEndBreak() ? $event->getEndBreak() : new \DateTime('13:30')
                        )
                    )
                ;
        }
    public function buildChild(FormBuilderInterface $builder, $event)
    {

        switch (get_class($event)) {
            case EventSimple::class:
                $this->buildEventSimple($builder,$event);
                break;

            case EventJobs::class:
                 $builder
                    ->add('registrationLimit', DateTimeType::class, array(
                            'label' => 'Date limite d\'inscription',
                             'widget' => 'choice',
                            'years' => array(date('Y'), date('Y')+1 ),
                            'format' => 'dMy',
                            'html5' => false
                        )
                    )
                    ->add('sectors', EntityType::class, array(
                                'class' => Sector::class,
                                'choice_label' => 'name',
                                'multiple' => true,
                                'expanded' => true,
                                'label' => 'Secteurs',
                                'required' => false,
                                'by_reference' => false
                            )
                    )
                ;

                /* if eventtype=escape
                    $builder->add('invitation', FileType::class, array(
                            'attr' => array('label' =>false),
                            'label' => "Modèle pour l'envoi d'invitations. Dimension : 1240x1754px . Résolution : 300dpi . Taille max : 2Mo",
                            'required' => false,
                             'file_constraints' => array(
                                new FileConstraint([
                                    'maxSize' => '2M',
                                    'mimeTypes' => ["image/jpeg", "image/jpg", "image/png"]
                                ]))
                        )
                    );
                */
                break;

            default:
                throw new \Exception("Error create event form. class not found : ".get_class($event), 1);

                break;
        }


    }
}
