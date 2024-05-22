<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminTypeUser extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrez un email',
                ]),
                new Length([
                    'max' => 180,
                    'maxMessage' => 'Votre nom doit comporter au moins 180 caractères',
                ]),
            ],
            'attr' => ['placeholder' => 'Votre email']
        ])
        ->add('nom', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrez un nom',
                ]),
                new Length([
                    'min' => 1,
                    'maxMessage' => 'Votre nom doit comporter au moins 180 caractères',
                    'max' => 180,
                ]),
            ],
            'attr' => ['placeholder' => 'Votre nom']
        ])
        ->add('prenom', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrez un prénom',
                ]),
                new Length([
                    'max' => 180,
                ]),
            ],
            'attr' => ['placeholder' => 'Votre prénom']
        ])
        ->add('tel', NumberType::class, [
            'attr' => [
                'pattern' => '[0-9]*',
                'placeholder' => 'Votre numéro de téléphone',
                'class' => 'form-control'
            ],
            'constraints' => [
                new NotBlank(['message' => 'Entrez un numéro de téléphone']),
                new Length(['max' => 15, 'maxMessage' => 'Votre numéro de téléphone doit comporter au maximum 15 chiffres']),
            ],
            'label' => 'Numéro de téléphone', // Ajoutez ou modifiez le label si nécessaire
        ])
    ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
