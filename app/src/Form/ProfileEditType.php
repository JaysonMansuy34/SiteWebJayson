<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'email ne peut pas être vide.']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide.'])
                ]
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom ne peut pas être vide.']),
                    new Length(['max' => 255])
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom ne peut pas être vide.']),
                    new Length(['max' => 255])
                ]
            ])
            ->add('tel', TelType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone ne peut pas être vide.']),
                    new Regex([
                        'pattern' => '/^\d{10}$/',
                        'message' => 'Le numéro de téléphone doit contenir 10 chiffres.'
                    ])
                ]
            ])
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,  // Le champ n'est pas requis pour soumettre le formulaire
                'constraints' => new UserPassword(['message' => 'Mot de passe actuel incorrect.']),
                'attr' => [
                    'placeholder' => 'Entrez votre mot de passe actuel',
                ],
                'label' => 'Mot de passe actuel',
            ])
            ->add('newPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.'
                    ])
                ],
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'Nouveau mot de passe (laissez vide pour ne pas changer)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
