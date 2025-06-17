<?php

namespace App\Form;

use App\Entity\CandidateUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('usernameCanonical')
            ->add('email')
            ->add('emailCanonical')
            ->add('enabled')
            ->add('salt')
            ->add('password')
            ->add('lastLogin')
            ->add('confirmationToken')
            ->add('passwordRequestedAt')
            ->add('roles')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('position')
            ->add('mailingEvents')
            ->add('mailingRecall')
            ->add('phoneRecall')
            ->add('wantedJob')
            ->add('cv')
            ->add('updatedAt')
            ->add('mobility')
            ->add('degree')
            ->add('city')
            ->add('sectors')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidateUser::class,
        ]);
    }
}
