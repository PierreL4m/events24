<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', ChoiceType::class, array(
                    'label' => "Ma demande ",
                    'choices' => array(
                        'Je suis visiteur' => 'Demande d\'informations',
                        'Je souhaite devenir exposant' => 'Contact "Je souhaite devenir exposant"',

                    )
                )
            )
            ->add('name', TextType::class, array(
                    'label' => 'Nom ',
                    'required' => true,
                    'constraints' => array(
                       new NotBlank()                     
                    ),
                ))
            ->add('firstName', TextType::class, array(
                    'label' => 'Prénom ',
                    'required' => true,
                    'constraints' => array(
                       new NotBlank()                     
                    ),
                ))
            ->add('phone', TelType::class, array(
                    'label' => 'Téléphone ',
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 10, 'max' => 20)                       
                    )                     
                   ),
                ))
            ->add('email', EmailType::class, array(
                'label' => 'Email ',
                'required' => true,
                'constraints' => array(
                       new NotBlank()                     
                   ),
            ))
             ->add('message', TextareaType::class, array(
                'label' => 'Message ',
                'required' => true,
                'constraints' => array(
                     new NotBlank(),
                    new Length(array('min' => 3)
                ))
            ))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
