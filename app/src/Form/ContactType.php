<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis.']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne peut pas dépasser 50 caractères.',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail :',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est requis.']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide.']),
                ]
            ])
            ->add('tel', TextType::class, [
                'label' => 'Numéro de téléphone :',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est requis.']),
                    new Regex([
                        'pattern' => '/^\+?[0-9\s\-\(\)]*$/',
                        'message' => 'Veuillez entrer un numéro de téléphone valide.',
                    ]),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
                    ]),
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Description :',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le message est requis.']),
                    new Length([
                        'min' => 10,
                        'max' => 1000,
                        'minMessage' => 'Le message doit contenir au moins 10 caractères.',
                        'maxMessage' => 'Le message ne peut pas dépasser 1000 caractères.',
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
