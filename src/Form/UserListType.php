<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\ExposantUser;
use App\Entity\Organization;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $organization = $options['data'];
        $id = $organization->getId();
        $builder            
            ->add('users', EntityType::class, array(
            'class' => ExposantUser::class,
            'query_builder' => function (EntityRepository $er) use ($id) {
                $qb = $er->createQueryBuilder('u');
                $qb ->where('u.organization = :id')
                    ->setParameter('id',$id)
                    ->distinct();
                return $qb;
            },
            'expanded' => false,
            'multiple' => false,
            'label' => false,            
            'mapped' => false
            )            
        );
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
        ]);
    }
}
