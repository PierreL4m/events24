<?php

namespace App\Form;

use App\Entity\Participation;
use App\Helper\GlobalHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationTypeList extends AbstractType
{
    private $helper;

    public function __construct(GlobalHelper $helper)
    {
        $this->helper = $helper;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = $this->helper->getReadableChildren(Participation::class);
        $type = $options['data']->getType();
        unset($types[$type]);

        $builder
            ->add('participation_type', ChoiceType::class,array(
                    'choices' => $types,
                    'label' => 'Attention seules les données de "type entreprises" seront conservées',
                    'mapped' => false
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participation::class
        ]);
    }
}
