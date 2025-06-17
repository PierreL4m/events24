<?php

namespace App\Form;

use App\Entity\Color;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	if ($options['super_admin']){
    		$builder->add('name',TextType::class, array(
            	'label' => false,
            	'attr' => array('placeholder' => 'color_1'),
            	'required' => false
            	)
        	);
    	}

        $builder	
            ->add('code', TextType::class, array(
            	'label' => false,
            	'attr' => array('placeholder' => 'Code hexa : pipette dans photoshop sur la couleur de la créa à Jéjé (Ex : #ffffff)')
            	)
        	)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Color::class,
            'super_admin' => false
        ));
    }
}
