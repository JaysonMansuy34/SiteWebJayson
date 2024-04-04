<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
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
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrez un numéro de téléphone',
                ]),
                new Length([
                    'max' => 15,
                    'maxMessage' => 'Votre numéro de téléphone doit comporter au maximum 15 chiffres',
                ]),
            ],
            'attr' => ['placeholder' => 'Votre numéro de téléphone']
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter les termes.',
                ]),
            ],
            'label' => 'J\'accepte les termes et conditions'
        ])
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => [
                'autocomplete' => 'new-password',
                'placeholder' => 'Votre mot de passe'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrez un mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                    'max' => 255,
                ]),
            ],
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
