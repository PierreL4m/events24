<?php

namespace App\Form\Api;

use App\Entity\CandidateParticipationComment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateParticipationCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['context'] == 'web'){
            $options['csrf_protection'] = true;
        }

        $builder->add('favorite', ChoiceType::class, 
                array(
                    'choices' => array(
                        'Oui' => true, 
                        'Non' => false
                    ),
                    'label' => false,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false
                ))
        ->add('like', ChoiceType::class, 
                array(
                    'choices' => array(
                        '-1' => -1, 
                        '1' => 1,
                        '0' => 0
                    ),
                    'label' => false,
                    'expanded' => false,
                    'multiple' => false,
                'required' => false
                ))
        ->add('comment', TextareaType::class, array(
                'label' => false,
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidateParticipationComment::class,
            'csrf_protection' => false,
            'context' => false
        ]);
    }
}
