<?php

namespace App\Form;

use App\Entity\ExposantUser;
use App\Entity\L4MUser;
use App\Entity\OnsiteUser;
use App\Entity\RecruitmentOffice;
use App\Entity\RhUser;
use App\Entity\User;
use App\Entity\CandidateUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $options['data'];
        $builder
            ->add('lastname',TextType::class, array(
                    'label' => 'Nom *',
                )
            )
            ->add('firstname',TextType::class, array(
                    'label' => 'Prénom *',
                )
            )
            ->add('email',EmailType::class, array(
                    'label' => 'Email *',
                )
            )
            ->add('phone',TextType::class, array(
                    'label' => 'Téléphone *',
                )
            )
            ->add('mobile',TextType::class, array(
                    'label' => 'Téléphone portable',
                    'required' => false
                )
            )
            ->add('position',TextType::class, array(
                    'label' => 'Fonction',
                    'required' => false
                )
            )
        ;
        $this->buildChild($builder,$user);
    }

    public function buildChild($builder,$user)
    {
        switch (get_class($user)) {
            case ExposantUser::class:
                $builder->add('responsableBises', CollectionType::class, array(
                    'entry_type' => ResponsableBisType::class,
                    'entry_options' => array('label' => false),
                    'label' => 'Email(s) en copie',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ));
                break;

                case CandidateUser::class:
                    $builder
                        ->add('sendPassword',ChoiceType::class,
                            array(
                                'choices'  => array(
                                    'Oui' => 1,
                                    'Non' => 0,
                                ),
                                'label' => 'Envoyer les changements de mot de passe par email?'
                            )
                        )
                        ->add('enabled',ChoiceType::class,
                            array(
                                'choices'  => array(
                                    'Oui' => 1,
                                    'Non' => 0,
                                ),
                                'label' => 'Actif ?'
                            )
                        )
                    ;
                    break;

            case RhUser::class:

                // $builder
                //     ->add('recruitmentOffice', EntityType::class, array(
                //         'class' => RecruitmentOffice::class,
                //         'choice_label' => 'name'
                //     )
                // ); does not work with RecruitmentOfficeType

                $builder->add('enabled',ChoiceType::class,
                    array(
                        'choices'  => array(
                            'Oui' => 1,
                            'Non' => 0,
                        ),
                        'label' => 'Actif ?'
                    )
                );
                break;

            case L4MUser::class:
                $builder
                    ->add('sendPassword',ChoiceType::class,
                        array(
                            'choices'  => array(
                                'Oui' => 1,
                                'Non' => 0,
                            ),
                            'label' => 'Envoyer les changements de mot de passe par email?'
                        )
                    )
                    ->add('enabled',ChoiceType::class,
                        array(
                            'choices'  => array(
                                'Oui' => 1,
                                'Non' => 0,
                            ),
                            'label' => 'Actif ?'
                        )
                    )
                ;
                break;

            case OnsiteUser::class:
                $builder
                    ->add('sendPassword',ChoiceType::class,
                        array(
                            'choices'  => array(
                                'Oui' => 1,
                                'Non' => 0,
                            ),
                            'label' => 'Envoyer les changements de mot de passe par email?'
                        )
                    )
                    ->add('enabled',ChoiceType::class,
                        array(
                            'choices'  => array(
                                'Oui' => 1,
                                'Non' => 0,
                            ),
                            'label' => 'Actif ?'
                        )
                    )
                ;
                break;

            default:
                throw new \Exception("Error create user form. class not found : ".get_class($user), 1);
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
