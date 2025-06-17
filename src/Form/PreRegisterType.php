<?php

namespace App\Form;

use App\Entity\CandidateUser;
use App\Entity\Degree;
use App\Entity\EventJobs;
use App\Entity\HeardFrom;
use App\Entity\L4MUser;
use App\Entity\Mobility;
use App\Entity\ScanUser;
use App\Entity\Sector;
use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\EventType;
use App\Entity\Job;
use App\Repository\JobRepository;
use App\Entity\Status;

class PreRegisterType extends AbstractType
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

            $mime_types = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.oasis.opendocument.text", "image/jpeg", "image/jpg", "image/png"];
            $types = 'pdf, doc, docx, odt, jpg ou png';


        if(array_key_exists('data', $options)){
            $candidate = $options['data'];
        }
        else{
            $candidate = null;
        }
        $new_candidate = true;

        if($candidate && $candidate->getId() != null){
            $new_candidate = false;
        }

        $user = $options['user'] ;

        if ($user && !($user instanceof CandidateUser) && !$options['edit_profile']){
            $new_candidate = true;
            //do not prefill candidate datas
        }

        if ($new_candidate || $options['edit_profile'] || $options['context'] == 'api'){
            $text_type = TextType::class;
            $email_type = EmailType::class;

            $builder
                ->add('lastname',$text_type, array(
                        'label' => 'Nom ',
                    )
                )
                ->add('firstname',$text_type, array(
                        'label' => 'Prénom ',
                    )
                )

                ->add('email',$email_type, array(
                        'label' => 'Email ',
                    )
                )
                ->add('phone',$text_type, array(
                        'label' => 'Téléphone portable',
                    )
                )
            ;
        }
        $event = $options['event'];

        //todo clean this
        $wanted_job_required = false;

        //end clean
        $required = false;
        $event_label = $event_label_recall = null;

            $password_constraint = null;

            if($new_candidate){
                $password_constraint = new NotBlank(['message' => 'Merci de saisir un mot de passe']);
            }


            $description = "This value is required only on first registration. It is not required when candidate is logged in, nor required when role = scan or role =l4m";

            $builder
                   ->add('plainPassword', PasswordType::class, array(
                     'label' => 'Mot de passe',
                        /*'documentation' => [
                            'type' => 'password',
                            'description' => $description

                        ],*/
                        'constraints' => $password_constraint
                    )
                )
            ;
            if($new_candidate || $options['edit_profile']){

               $builder->add('mailingRecall', CheckboxType::class,
                    array(
                        'label' => 'Je souhaite recevoir par email des informations relatives à ce type d\'événement',
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
            'cv' => null
        ]);
    }
}
