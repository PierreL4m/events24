<?php

namespace App\Form;

use App\Entity\Email;
use App\Entity\Participation;
use App\Entity\Partner;
use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MailTypeCandidats extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $email = $options['data'];
        $event_id = $email->getEvent()->getId();
        $email_type = $email->getEmailType()->getSlug();
        $accepted_format = $email->getAcceptedFormat();
        $builder
            ->add('subject', TextType::class,array(
                'label' => 'Sujet *'
                )
            )
            ->add('body', TextareaType::class,array(
                'label' => 'Corps du mail *',
                'required' => false
                )
            )
        ;
        $builder->add('recipients', EntityType::class, array(
            'class' => CandidateParticipation::class,
            'query_builder' => function (EntityRepository $er) use ($event_id) {
                return $er->createQueryBuilder('c')
                    ->join('c.event', 'e')
                    ->join('c.candidate', 'cd')
                    ->where('e.id = :id')
                    ->andWhere('c.status = 2')
                    ->setParameter('id',$event_id);
            },
            'expanded' => true,
            'multiple' => true,
            'choice_label' =>  function ($candidate) {
                $label = $candidate->getCandidate()->getFirstname().' - '.$candidate->getCandidate()->getEmail();
                return $label;
            },
            'label' => 'Destinataires',
            'mapped' => false,
            'choice_attr' => function ($val, $key, $index) {
               return array('checked' => true);
            }
        ));

        $builder
            ->add('attachmentFile',
                FileType::class,
                array(
                    'label' => 'Pièce Jointe (max 1024KB (1MB))',
                    'required' => false,
                    'constraints' => array(
                        new File(
                            array (
                                'maxSize' => '1100K',
                                'mimeTypes' => $accepted_format
                            )
                        )
                    )
                )
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
        ]);
    }
}
