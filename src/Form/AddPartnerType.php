<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Partner;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = $builder->getData();
        $id = $options['partner_type_id'] ;

        $builder->add('partners', EntityType::class, array(
            'class' => Partner::class,
            'query_builder' => function (EntityRepository $er) use ($id) {
                return $er->createQueryBuilder('p')
                    ->join('p.partnerType', 'pt')
                    ->where('pt.id = :id')
                    ->setParameter('id', $id)
                    ;
            },
            'expanded' => true,
            'multiple' => true,
            'label' => false,
            'by_reference' => false
            )            
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'partner_type_id' => null
        ]);
    }
}


