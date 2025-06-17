<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType as File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class FileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //dump($options['constraints'][0]); die();
        $builder
            ->add('file',File::class,
                array(
                    'label' => false,
                    'required' => $options['required'],
                    'constraints' => $options['file_constraints']
                )
            )
        ;

        if ($options['alt_label']){
            $builder->add('alt',TextType::class,
                array(
                    'label' => $options['alt_label']
                )
            )
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fbenoit\ImageBundle\Entity\Image',
            'label' => 'Image',
            'alt_label' => false,
            'required' => true,
            'file_constraints' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fbenoitbundle_image';
    }
}