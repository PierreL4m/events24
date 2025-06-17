<?php

namespace App\Form\Api;

use App\Entity\CandidateUser;
use App\Entity\Degree;
use App\Entity\EventJobs;
use App\Entity\L4MUser;
use App\Entity\Mobility;
use App\Entity\OnsiteUser;
use App\Entity\Sector;
use App\Entity\Slots;
use App\Entity\Origin;
use App\Repository\OriginRepository;
use App\Repository\SlotsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\EventType;
use App\Entity\Job;
use App\Repository\JobRepository;
use App\Entity\Status;

class RegistrationType extends AbstractType
{

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker=null;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        if($options['context'] != 'api'){
            $options['csrf_protection'] = true;
            $mime_types = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.oasis.opendocument.text","application/octet-stream","application/zip", "image/jpeg", "image/jpg", "image/png"];
            $types = 'pdf, doc, docx ou odt, jpg ou png';
        }
        else{
            $mime_types = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.oasis.opendocument.text", "image/jpeg", "image/jpg", "image/png"];
            $types = 'pdf, doc, docx, odt, jpg ou png';
        }

        if(array_key_exists('data', $options)){
            $candidate = $options['data'];
        }
        else{
            $candidate = null;
        }
        $new_candidate = true;
        if($candidate && $candidate->getId() != null){
            $new_candidate = false;
            $connected_candidate = true;
        }

        $user = $options['user'] ;

        if ($user && !($user instanceof CandidateUser) && !$options['edit_profile']){
            $new_candidate = true;
            //do not prefill candidate datas
        }
        $event = $options['event'];
        $second_event = $options['second_event'];

        if ($new_candidate || $options['edit_profile']  || $connected_candidate){
            $text_type = TextType::class;
            $email_type = EmailType::class;
            $builder
                ->add('lastname',$text_type, array(
                        'label' => 'Nom ',
                        'constraints' => [
                            new NotNull(['message' => 'Merci de remplir ce champ']),
                        ],
                        'attr' => array('autocomplete' => 'off')
                    )
                )
                ->add('firstname',$text_type, array(
                        'label' => 'Prénom ',
                        'constraints' => [
                            new NotBlank(['message' => 'Merci de remplir ce champ']),
                        ],
                        'attr' => array('autocomplete' => 'off')
                    )
                )

                ->add('email',$email_type, array(
                        'label' => 'Email ',
                        'constraints' => [
                            new NotBlank(['message' => 'Merci de remplir ce champ']),
                        ],
                        'attr' => array('autocomplete' => 'off')
                    )
                )
                ->add('phone',$text_type, array(
                        'label' => 'Téléphone portable',
                        'constraints' => [
                            new NotBlank(['message' => 'Merci de remplir ce champ']),
                        ],
                        'attr' => array('autocomplete' => 'off')
                    )
                )
            ;
        }

        if ($new_candidate || $options['edit_profile'] || $options['context'] == 'api' || $connected_candidate){
            if($options['slots']){
                if($second_event == NULL || ($second_event != NULL && $event->getDate()->format('Y-m-d') >= date('Y-m-d') )){
                    if($second_event != NULL){
                        $builder->add('slots', EntityType::class, array(
                            'class' => Slots::class,
                            'query_builder' => function (SlotsRepository $er) use ($event) {
                                $qb = $er->createQueryBuilder('s');
                                $qb ->where('s.is_full = false')
                                    ->andWhere('s.event = '.$event->getId())
                                ;
                                return $qb;
                            },
                            'mapped' => false,
                            'expanded' => true,
                            'multiple' => false,
                            'label' => 'Veuillez choisir un créneau pour l\'événement du '.$event->getDate()->format('d/m/Y').'*',
                            'required' => false,
                            'placeholder' => 'Je ne serai pas présent·e le '.$event->getDate()->format('d/m/Y')
                        ))
                        ;
                    }else{
                        $builder->add('slots', EntityType::class, array(
                            'class' => Slots::class,
                            'query_builder' => function (SlotsRepository $er) use ($event) {
                                $qb = $er->createQueryBuilder('s');
                                $qb ->where('s.is_full = false')
                                    ->andWhere('s.event = '.$event->getId())
                                ;
                                return $qb;
                            },
                            'mapped' => false,
                            'expanded' => true,
                            'multiple' => false,
                            'label' => 'Veuillez choisir un créneau pour l\'événement du '.$event->getDate()->format('d/m/Y').'*',
                            'required' => true
                        ))
                        ;
                    }


                    $builder->add('slotsFull', EntityType::class, array(
                        'class' => Slots::class,
                        'query_builder' => function (SlotsRepository $er) use ($event) {
                            $qb = $er->createQueryBuilder('s');

                            $qb
                                ->where('s.is_full = true')
                                ->andWhere('s.event = '.$event->getId())
                            ;
                            return $qb;
                        },
                        'disabled' => true,
                        'mapped' => false,
                        'expanded' => true,
                        'multiple' => false,
                        'label' => "Créneaux Complets",
                        'required' => true,
                        'empty_data' => 'false'
                    ))
                    ;

                }

                if($second_event != NULL){
                    $builder->add('slots_second', EntityType::class, array(
                        'class' => Slots::class,
                        'query_builder' => function (SlotsRepository $er) use ($second_event) {
                            $qb = $er->createQueryBuilder('s');

                            $qb ->where('s.is_full=0')
                                ->andWhere('s.event = '.$second_event->getId())
                            ;
                            return $qb;
                        },
                        'mapped' => false,
                        'expanded' => true,
                        'multiple' => false,
                        'label' => 'Veuillez choisir un créneau pour l\'événement du '.$second_event->getDate()->format('d/m/Y').'*',
                        'required' => false,
                        'placeholder' => 'Je ne serai pas présent·e le '.$second_event->getDate()->format('d/m/Y')
                    ))
                    ;

                    $builder->add('slotsFull_second', EntityType::class, array(
                        'class' => Slots::class,
                        'query_builder' => function (SlotsRepository $er) use ($second_event) {
                            $qb = $er->createQueryBuilder('s');

                            $qb
                                ->where('s.is_full = 1')
                                ->andWhere('s.event = '.$second_event->getId())
                            ;
                            return $qb;
                        },
                        'disabled' => true,
                        'mapped' => false,
                        'expanded' => true,
                        'multiple' => false,
                        'label' => "M'inscrire pour ce créneau",
                        'empty_data' => 'false',
                        'required' => true
                    ))
                    ;

                }
            }
        }

        $event = $options['event'];

        if($event && $event->getType()->getRegistrationType() == EventType::REGISTRATION_TYPE_JOB) {
            $builder->add('job', EntityType::class, array(
                'class' => Job::class,
                'query_builder' => function (JobRepository $er) use ($event) {
                    $qb = $er->createQueryBuilder('j');

                    $qb->join('j.participation', 'p')
                        ->join('p.event', 'e')
                        ->where('e.id = :id')
                        ->setParameter('id', $event->getId())
                        ->orderBy('p.companyName', 'ASC')
                    ;
                    return $qb;
                },
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'label' => "M'inscrire pour ce poste",
                'choice_label' =>  function (Job $job) {
                    $label = $job->getParticipation()->getCompanyName().' - '.$job->getName();
                    return $label;
                },
                'constraints' => [
                    new NotBlank(['message' => 'Merci de choisir le poste']),
                ],
                'required' => true
            ))
            ;
        }

        //todo clean this
        $wanted_job_required = false;

        if($event &&
            ($event instanceof EventJobs && ($new_candidate || !$candidate->getWantedJob()))
            && $options['context'] != 'api'
        ){
            $wanted_job_required = true;
        }
        //end todo
        if($event &&
            ($event->getType()->getRegistrationType() == EventType::REGISTRATION_TYPE_EXTENDED && ($new_candidate || !$candidate->getWantedJob()))
            || $options['edit_profile']
        ){

            $builder
                ->add('wantedJob',TextareaType::class, array(
                        'label' => 'Poste recherché',
                        'required' => $wanted_job_required
                    )
                );

            /*
            ->add('heardFrom', EntityType::class , array(
                    'label' => 'Comment avez vous entendu parler de l\'événement ?',
                    'class' => HeardFrom::class,
                    'constraints' => [
                            new NotBlank(['message' => 'Merci de choisir une option']),
                        ],
                    'attr' => array('placeholder' => 'Choisir'),
                    'mapped' => false,
                    'empty_data' => 'Choisir'
                )
            )*/

        }
        
        if(($event && $event instanceof EventJobs) || $options['edit_profile'] ) {
            $builder->add('working', ChoiceType::class, array(
                    'label' => 'En poste ?',
                    'choices' => array(
                        'Oui' => true,
                        'Non' => false
                    ),
                    'expanded' => true,
                    'multiple' => false
                )
            )->add('origin', EntityType::class, array(
                    'label' => 'Comment avez vous connu l\'événement',
                    'class' => Origin::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'placeholder' => 'Sélectionnez une provenance',
                    'query_builder' => function(OriginRepository $or){
                        return $or->createQueryBuilder('o');
                    }
                )
            );
        }
        
        //to do clean this
//        if ($options['edit_profile'] ){
//            $builder
//            ->add('sectors', EntityType::class, array(
//                    'label' => 'Secteur(s) recherché(s) ',
//                    'class' => Sector::class,
//                    'expanded' => true,
//                    'required' => false,
//                    'by_reference' => false,
//                    'multiple' => true
//                )
//            )->add('mobility',EntityType::class, array(
//                    'label' => 'Mobilité ',
//                    'class' => Mobility::class,
//                    'expanded' => true,
//                    'choice_label' => 'name',
//                    'constraints' => [
//                        new NotBlank(['message' => 'Merci de choisir votre mobilité']),
//                    ],
//                    'required' => false
//                )
//            )
//                ->add('degree',EntityType::class, array(
//                        'label' => 'Niveau d\'études actuel ',
//                        'class' => Degree::class,
//                        'expanded' => true,
//                        'choice_label' => 'name',
//                        'constraints' => [
//                            new NotBlank(['message' => 'Merci de choisir votre diplôme']),
//                        ],
//                        'required' => false
//                    )
//                )
//            ;
//        }

        if($options['context'] == 'admin' && ($this->authorizationChecker->isGranted('ROLE_RH') || $this->authorizationChecker->isGranted('ROLE_VIEWER'))) {
            $builder->add('rhSectors', EntityType::class, array(
                'label' => 'Secteur(s) RH ',
                'class' => Sector::class,
                'expanded' => true,
                'multiple' => true,
                'by_reference' => false,
                'required' => false
            ));

            if(!$event->getType()->registrationValidationAuto()) {
                $builder->add('status', EntityType::class, array(
                    'class' => Status::class,
                    'mapped' => false,
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                    'label' => "Statut de la participation",
                    'choice_label' =>  function (Status $status) {
                        return $status->getName();
                    },
                ))
                ;
            }
        }

        //end clean
        $required = false;
        $cv_constraint = null;
        $cv_array_constraint =[
            new File([
                'maxSize' => '10M',
                'mimeTypes' => $mime_types,
                'mimeTypesMessage' => 'Merci de charger un fichier '.$types.' valide',
            ])
        ];

        if(($options['context'] != 'admin' && $new_candidate && !$options['cv']) || !$options['edit_profile']){
            $cv_constraint = new NotNull(['message' => 'Merci de fournir votre CV']);
            array_push($cv_array_constraint,$cv_constraint);
            $required = true;
        }else{
            $required = false;
        }
        $event_label = $event_label_recall = null;

        //context web
        if ($options['context'] != 'api'){
            if ($candidate && $candidate->getCity()){
                $city = $candidate->getCity();
                $city_id = $city->getId();
            }
            else{
                $city=$city_id=null;
            }

            if (!$new_candidate){
                $cv_label = 'Modifier ';
                $event_label = 'aux événements auxquels je m\'inscris';
                $event_label_recall = 'des événements auxquels je m\'inscris';
            }
            else{
                $cv_label = 'Ajouter ';
                $event_label = 'à cet événement';
                $event_label_recall = "de cet événement";
            }

            if (is_string($options['cv'])){
                $cv_label = 'Modifier ';
            }
            if($options['edit_profile']){
                $password_label = 'Modifier votre m';
            }
            else{
                $password_label = 'M';
            }

            if ($options['edit_profile']){
                $builder->add('city_name',TextType::class, array(
                        'label' => 'Ville ',
                        'mapped' => false,
                        'data' => $city,
                        'constraints' => [
                            new NotBlank(['message' => 'Merci de choisir une ville dans la liste. (merci de commencer à taper votre ville pour faire apparaître la liste)']),
                            new NotNull(['message' => 'Merci de choisir une ville dans la liste. (merci de commencer à taper votre ville pour faire apparaître la liste)'])
                        ],
                        'attr' => array('list' =>'listCities' ),
                        'required' => false
                    )
                )
                    ->add('city_id',HiddenType::class, array(
                            'data' => 0,
                            'attr' => array('list' =>'listCities' ),
                            'mapped' => false
                        )
                    );
            }

            if ($user instanceof L4MUser){
                $builder->add('file', FileType::class, array(
                        'label' => $cv_label.'votre CV '.$types.' (max 10Mo)',
                        'required' => false
                    )
                );
            }elseif ($user instanceof OnsiteUser){
                $builder->add('file', FileType::class, array(
                        'label' => 'Prendre le CV en photo',
                        'required' => $required,
                        'constraints' => [
                            new NotBlank(['message' => 'Merci de fournir votre CV'])
                        ],
                        'attr' => array('capture' => 'environment', 'accept' => 'image/*')
                    )
                );
            }else{
                $builder->add('file', FileType::class, array(
                        'label' => $cv_label.'votre CV '.$types.' (max 10Mo)',
                        'required' => $required,
                        'constraints' => [
                            new NotBlank(['message' => 'Merci de fournir votre CV'])
                        ]
                    )
                );
            }

            if ($new_candidate || $options['edit_profile']){
                if( $user instanceof CandidateUser || !is_object($user)){
                    $builder->add('plainPassword', PasswordType::class, array(
                            'label' => $password_label.'ot de passe (8 caractères minimum, avec au moins un chiffre, une majuscule et une minuscule)',
                            'required' => $required
                        )
                    )
                    ;
                }
            }
        }
        //context = api
        else{

            $cv_constraint = [new Length(['max' => '10000000'])];
            
            $password_options = array(
                'label' => 'Créez votre mot de passe'
            );

            if($new_candidate){
                $password_options['constraints'] = new NotBlank(['message' => 'Merci de saisir un mot de passe']);
                // does not work if clearmissing set to false
                array_push($cv_constraint,new NotBlank(array('message' => 'Merci d\'ajouter un CV')));
            }
              
            $builder->add('city')
                ->add('cv_file', TextareaType::class, array(
                        'label' => 'Ajoutez votre CV (pdf, doc, docx ou odt, jpg ou png)',
                        'mapped' => false,
                        'constraints' =>
                            $cv_constraint
                    )
                )
                ->add('plainPassword', PasswordType::class, $password_options)
            ;
        }
        
        if ($options['context'] == 'api'){
            $builder
                ->add('mailingEvents', ChoiceType::class,
                    array(
                        'choices' => array(
                            'Oui' => true,
                            'Non' => false
                        ),
                        'label' => 'Je souhaite recevoir par email des informations sur les salons emploi/formation à venir dans ma région ',
                        'expanded' => true,
                        'multiple' => false
                    )
                )
                ->add('mailingRecall', ChoiceType::class,
                    array(
                        'label' => 'Je souhaite recevoir par email des informations relatives à cet événement',
                        'choices' => array(
                            'Oui' => true,
                            'Non' => false
                        ),
                        'expanded' => true,
                        'multiple' => false,
                    )
                )
                ->add('phoneRecall', ChoiceType::class,
                    array(
                        'label' => 'Je souhaite recevoir un rappel par sms la veille de cet événement',
                        'choices' => array(
                            'Oui' => true,
                            'Non' => false
                        ),
                        'expanded' => true,
                        'multiple' => false
                    )
                )
            ;
        }
        //context = web
        else{
            if($new_candidate || $options['edit_profile']){

                $builder->add('mailingRecall', CheckboxType::class,
                    array(
                        'label' => 'Je souhaite recevoir par email des informations relatives '.$event_label,
                        'required' => false,
                        //  'label_attr' => array(
                        //     'class' => 'checkbox-inline'
                        // )
                    )
                )
                    ->add('mailingEvents', CheckboxType::class,
                        array(
                            'label' => 'Je souhaite recevoir par email des informations sur les salons emploi/formation à venir dans ma région ',
                            'required' => false
                        )
                    )
                    ->add('phoneRecall', CheckboxType::class,
                        array(
                            'label' => 'Je souhaite recevoir un rappel par sms la veille '.$event_label_recall,
                            'required' => false
                        )
                    )
                ;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidateUser::class,
            'csrf_protection' => false,
            'context' => false,
            'event' => null,
            'user' => null,
            'edit_profile' => false,
            'password' => null,
            'cv' => null,
            'slots' => 0,
            'second_event' => null
        ]);
    }
}
