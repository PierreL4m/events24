<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Organization;
use App\Entity\OrganizationType as OrganizationTypeEntity;
use App\Entity\Sector;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $organization = $options['data'];
        $id = $organization->getId();

        $builder
            ->add('name',TextType::class, array(
                    'label' => 'Nom *'
                )
            )
            ->add('internalName',TextType::class, array(
                    'label' => 'Nom interne',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'visible uniquement par l\'équipe L4M, facultatif' 
                    )
                )
            )
            ->add('organizationTypes',EntityType::class, array(
                    'class' => OrganizationTypeEntity::class,
                    'label' => 'Type d\'organisme *',
                    'expanded' => true,
                    'multiple' => true,
                    'by_reference' => false,
                )
            )
        ;
        if (!$options['edit']){
            $builder->add('events', EntityType::class, array(
                'class' => Event::class,
                'query_builder' => function (EntityRepository $er) use ($id){
                    $qb2 = $er->createQueryBuilder('e2')
                        ->select('e2.id')
                        ->join('e2.participations','p2')
                        ->join('p2.organization', 'o2')
                        ->where('o2.id != :id')
                        ->getDql();

                    $qb = $er->createQueryBuilder('e');

                    $qb
                        ->where('e.date > :now')
                        ->setParameter('now' , new \DateTime())
                        ->orderBy('e.date', 'ASC')
                        ;
                    
                    if($id){
                        $qb->where($qb->expr()->notIn('e.id',$qb2))
                            ->setParameter('id', $id);
                    }

                    return $qb;
                },
                'label' => 'Ajouter à l\'événement',
                'expanded' => true,
                'multiple' => true,
                'mapped' => false
                )            
            )
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
            'edit' => false
        ]);
    }
}
