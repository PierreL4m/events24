<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\L4MUser;
use App\Entity\OrganizationType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeManagerType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
               ->add('manager', EntityType::class,
                    array(
                        'class' => L4MUser::class,
                        'expanded' => true,
                        'multiple' => false,
                        'query_builder' => function (EntityRepository $er) {

                            $qb = $er->createQueryBuilder('u');
                            $qb2 = $er->createQueryBuilder('u2')
                                ->select('u2.id')
                                ->where('u.roles like :role')
                                ->orWhere('u.roles like :role2')
                                ->getDQL();

                            return $qb
                                ->where('u.enabled = true')
                                ->andWhere($qb->expr()->notIn('u.id', $qb2))
                                ->orderBy('u.username', 'ASC')
                                ->setParameters(array('role'=> '%ROLE_SUPER_ADMIN%', 'role2' => '%ROLE_VIEWER%'));
                        },
                        'label' => 'Nouveau responsable',
                    )
                )
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class
        ]);
    }
}
