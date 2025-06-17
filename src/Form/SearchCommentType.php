<?php

namespace App\Form;

use App\Entity\Bat;
use App\Entity\Participation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('searchComment', SearchFieldType::class, array(
                'label' => false,
                'required' =>false,
                'attr' => ['class' => 'searchCandidatScanned'],
                'placeholder' => "Nom / Prénom"
            ))
        ;
    }
}


