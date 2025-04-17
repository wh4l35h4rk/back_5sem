<?php

namespace App\Form;

use App\Entity\ProjectGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;


class ProjectGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'name should be at least 3 characters long',
                        'maxMessage' => 'name should be at most 255 characters long',
                    ]),
                ]
            ],)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectGroup::class,
        ]);
    }
}
