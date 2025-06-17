<?php

namespace App\Form;

use App\Entity\ExposantScanUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ExposantScanUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class,
                array(
                    'label' => 'Identifiant'                    
                )
            )
            ->add('savedPlainPassword', TextType::class, 
                array(
                    'label' => 'Mot de passe (6 caractères minimum avec au moins une minuscule et une majuscule)'                    
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExposantScanUser::class,
            'validation_groups' => false,
        ]);
    }
}
