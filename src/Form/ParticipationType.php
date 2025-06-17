<?php

namespace App\Form;

use App\Entity\ContractType;
use App\Entity\Participation;
use App\Entity\ParticipationCompanySimple;
use App\Entity\ParticipationDefault;
use App\Entity\ParticipationFormationSimple;
use App\Entity\ParticipationJobs;
use App\Entity\Street;
use App\Entity\Sector;
use App\Helper\TwigHelper;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\HttpFoundation\File\File;

class ParticipationType extends AbstractType
{
    private $helper;
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage, TwigHelper $helper)
    {
         $this->user = $tokenStorage->getToken()->getUser();
         $this->helper = $helper;
    }

/**********************************************************************************/
/**********************************************************************************/
/*************************** WARNING **********************************************/
/**********************************************************************************/
/*** this form is rendered manually if you add field add it in view too************/
/**********************************************************************************/
/*********************************************************************************/

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $participation = $options['data'];

        if ($this->user->hasRole('ROLE_SUPER_ADMIN') ){
            $builder->add('slug', TextType::class, array(
                    'required' => false,
                )
            );
        }

        // TODO : we should use an AuthorizationCheckerInterface to check if ROLE_ADMIN is granted
        if ($this->user->hasRole('ROLE_ADMIN') || $this->user->hasRole('ROLE_SUPER_ADMIN')){
            $builder->add('premium', CheckboxType::class, array(
                    'required' => false,

                    )
                )
                ->add('file', VichFileType::class, array(
                        'attr' => array('label' =>false),
                        'label' => "Fichier Publicitaire (Grand format 728 x 90)",
                        'required' => false,
                        //'by_reference' => false
                    )
                )
                ->add('fileMobile', VichFileType::class, array(
                        'attr' => array('label' =>false),
                        'label' => "Fichier Publicitaire (Format Mobile 640 x 100)",
                        'required' => false,
                        //'by_reference' => false
                    )
                )
                ->add('startPub', DateTimeType::class, array(
                        'label' => 'Date de mise en ligne de la pub',
                        'widget' => 'choice',
                        'years' => array(date('Y'), date('Y')+1 ),
                        'format' => 'dMy',
                        'required' => false,
                        'html5' => false
                    )
                )
                ->add('endPub', DateTimeType::class, array(
                        'label' => 'Date de mise hors ligne de la pub',
                        'widget' => 'choice',
                        'years' => array(date('Y'), date('Y')+1 ),
                        'format' => 'dMy',
                        'required' => false,
                        'html5' => false
                    )
                )
                //$event->getOnline() ? $event->getOnline() :(new \DateTime())->setTime("10","30")
            ;
        }
        $builder
            ->add('companyName', TextType::class, array(
                    'label' => 'Nom *',
                )
            )
            ->add('presentation', TextareaType::class, array(
                    'label' => 'Présentation *',
                    'required' => false
                )
            )

            ->add('sector',EntityType::class, array(
                    'label' => 'Secteur d\'activité (Basé sur le secteur d\'activité le plus représenté sur les offres)',
                    'class' => Sector::class,
                )
            )
            ->add('sites', CollectionType::class, array(
                    'entry_type' => ParticipationSiteType::class,
                    'entry_options' => array('label' => false,'user' => $this->user, 'helper' => $this->helper),
                    'label' => 'Site(s) web',
                    'allow_add' => true,
                    'allow_delete' => true
                )
            )
        ;

        $participation = $options['data'];
//        $this->buildChild($builder,$participation);

        $builder->add('facebook', TextType::class, array(
                    'label' => 'Facebook',
                    'attr' => array(
                        'placeholder' => 'www.facebook.com/votre_entreprise'
                    ),
                    'required' => false
                )
            )
            ->add('instagram', TextType::class, array(
                        'label' => 'Instagram',
                        'attr' => array(
                            'placeholder' => '/www.instagram.com/votre_entreprise'
                        ),
                        'required' => false
                    )
                )
            ->add('twitter', TextType::class, array(
                    'label' => 'Twitter',
                    'attr' => array(
                        'placeholder' => 'twitter.com/votre_entreprise'
                    ),
                    'required' => false
                )
            )
            ->add('viadeo', TextType::class, array(
                    'label' => 'Viadeo',
                    'attr' => array(
                        'placeholder' => 'fr.viadeo.com/votre_entreprise'
                    ),
                    'required' => false
                )
            )
            ->add('linkedin', TextType::class, array(
                    'label' => 'Linkedin',
                    'attr' => array(
                        'placeholder' => 'fr.linked.com/votre_entreprise'
                    ),
                    'required' => false
                )
            )
            ->add('th', CheckboxType::class, array(
                    'label' => 'Votre entreprise mène une politique RH en faveur de l\'insertion des personnes en situation de handicap',
                    'required' => false
                )
            )
            ->add('div', CheckboxType::class, array(
                    'label' => 'Votre entreprise mène une politique RH en faveur de la Diversité',
                    'required' => false
                )
            )
            ->add('senior', CheckboxType::class, array(
                    'label' => 'Votre entreprise mène une politique RH en faveur des Seniors',
                    'required' => false
                )
            )
            ->add('jd', CheckboxType::class, array(
                    'label' => 'Votre entreprise mène une politique RH en faveur des jeunes diplomés',
                    'required' => false
                )
            )

            ->add('youtube', TextType::class, array(
                    'label' => 'Lien de votre vidéo youtube',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Merci de copier coller l\'url de votre vidéo youtube'
                    )
                )
            )
            ->add('addressL1', TextType::class, array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Ex: Zone Industrielle'
                    )
                )
            )
            ->add('addressNumber',TextType::class, array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'n°'
                    )
                )
            )
            ->add('street',EntityType::class, array(
                    'label' => false,
                    'required' => false,
                    'class' => Street::class,
                    'placeholder' => 'Voie',
                    'empty_data' => null
                )
            )
            ->add('addressL2',TextType::class, array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Adresse'
                    )
                )
            )
            ->add('addressL3',TextType::class, array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Ex: BP 50'
                    )
                )
            )
            ->add('cp',TextType::class, array(
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Code postal *'
                    )
                )
            )
            ->add('city',TextType::class, array(
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Ville *'
                    )
                )
            )

//            ->add('info', TextType::class, array(
//                    'label' => 'Informations supplémentaires',
//                    'required' => false
//                )
//            )

            ->add('contactTitle',ChoiceType::class, array(
                    'label' => false,
                    'required' => false,
                    'placeholder' => 'Civilité',
                    'choices' => array('M.' => 'M.', 'Mme' => 'Mme')
                )
            )
            ->add('contactName',TextType::class, array(
                    'label' => 'Nom',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Nom'
                    )
                )
            )
            ->add('contactFirstName',TextType::class, array(
                    'label' => 'Prénom',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Prénom'
                    )
                )
            )
            ->add('contactEmail',TextType::class, array(
                    'label' => 'Email',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'email'
                    )
                )
            )
            ->add('contactPhone',TextType::class, array(
                    'label' => 'Téléphone',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Téléphone'
                    )
                )
            )

            ->add('logoFile', VichImageType::class, array(
                    'attr' => array('label' =>false),
                    'label' => "Logo",
                    'required' => false,
                    //'by_reference' => false
                )
            )
            ;

            //->add('standNumber')
            //->add('standType')


        ;
    }
//    public function buildChild(FormBuilderInterface $builder, $participation)
//    {
//        function addDescription($builder,$participation){
//            $builder
//                    ->add('description', TextareaType::class,array(
//                        'label' => $participation->getLabel().' *',
//                        'required' => false
//                        )
//                );
//        }
//
//        switch (get_class($participation)) {
//            case ParticipationFormationSimple::class:
//                addDescription($builder, $participation);
//                break;
//
//            case ParticipationCompanySimple::class:
//                addDescription($builder, $participation);
//                $builder->add('contractTypes', EntityType::class,array(
//                        'label' => $participation->getContractTypeLabel(),
//                        'class' => ContractType::class,
//                        'expanded' => true,
//                        'multiple' => true,
//                        'required' => false
//                    )
//                );
//                break;
//
//            case ParticipationJobs::class:
//                if ($this->helper->isAtLeastViewer($this->user)){
//                    $builder->add('maxJobs', IntegerType::class,array(
//                                'label' => 'Nombre de postes maximum'
//                            )
//                        )
//                    ;
//                }
//                $builder->add('jobs', CollectionType::class, array(
//                            'entry_type' => JobType::class,
//                            'entry_options' => array('label' => false,),
//                            'label' => false,
//                            'allow_add' => true,
//                            'allow_delete' => true,
//                            'by_reference' => false
//                        )
//                    )
//                ;
//                //joblinks
//                //gamesessions
//                //jobs
//
//
//                break;
//
//            case ParticipationDefault::class:
//                break;
//
//            default:
//                throw new \Exception("Error form participation. class not found : ".get_class($participation), 1);
//
//                break;
//        }
//
//
//    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
