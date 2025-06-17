<?php

namespace App\Form;

use App\Entity\RecruitmentOffice;
use App\Entity\RhUser;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecruitmentOfficeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ro = $options['data'];

        $rhs = $ro->getRhs();

        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nom du cabinet de recrutement'
                )
            )
        ;
        
        if(!$ro->getId() && count($rhs) == 1){
            $builder->add('rhs', CollectionType::class, [
                    'entry_type' => UserType::class,
                    'entry_options' => [
                        'data_class' => RhUser::class,
                        'data' => $rhs[0],
                        'label' => false
                    ],
                    'label' => 'Ajouter le premier utilisateur'
                ]
            )
            // ->add('rhs', UserType::class, array(
            //         //'entry_options' => [
            //             'data' => RhUser::class,
            //         //],
            //         'label' => 'Utilisateurs'
            //     )
            // )
            ;
        }
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RecruitmentOffice::class,
        ]);
    }
}
