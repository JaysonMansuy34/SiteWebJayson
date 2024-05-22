<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Contact;
use App\Entity\User;
use App\Form\AvisType;
use App\Form\ContactType;
use App\Form\ProfileEditType;
use App\Repository\AvisRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\RegistrationFormType;



class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, SendMailService $mail, Security $security): Response
    {
        $contact = new Contact();

        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Si l'utilisateur est connecté, pré-remplir le formulaire
        if ($user) {
            $contact->setEmail($user->getEmail());
            $contact->setNom($user->getNom()); // Assurez-vous que la méthode getNom existe et renvoie le nom
            $contact->setTel($user->getTel()); // Assurez-vous que la méthode getTel existe et renvoie le numéro de téléphone
        }

        $contactForm = $this->createForm(ContactType::class, $contact);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted()) {
            if ($contactForm->isValid()) {
                $entityManager->persist($contact);
                $entityManager->flush();

                $mail->send(
                    $contact->getEmail(),
                    'jaysonmansuy@icloud.com',
                    "Demande de rendez-vous",
                    'reservationClient',
                    compact('contact')
                );


                $mail->send(
                    'jaysonmansuy@icloud.com',
                    $contact->getEmail(),
                    "Demande de rendez-vous",
                    'reservation',
                    compact('contact')
                );

                $this->addFlash('success', 'Votre message a été envoyé avec succès!');
                return $this->redirectToRoute('homepage');
            } else {
                $errorMessage = 'Une erreur est survenue lors de l\'envoi du message.';
                $errors = $contactForm->getErrors(true);
                $errorMessage .= $this->formatFormErrors($errors);

                $request->getSession()->set('showContactForm', true);
            }
        }

        return $this->render('home/accueil.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }

    private function formatFormErrors(FormErrorIterator $errors): string
    {
        $errorMessages = '';
        foreach ($errors as $error) {
            // Obtenez le nom du champ et le message d'erreur
            $fieldName = $error->getOrigin()->getName();
            $errorMessages .= sprintf("\n- %s: %s", $fieldName, $error->getMessage());
        }
        return $errorMessages;
    }

    #[Route('/clear-session-flag', name: 'clear_session_flag')]
    public function clearSessionFlag(Request $request): Response
    {
        $request->getSession()->remove('showContactForm');
        return new Response(null, 204);
    }


    #[Route('/parcours/jayson', name: 'parcourspage')]
    public function parcours(): Response
    {
        return $this->render('home/detailsProfile.html.twig');
    }

    #[Route('/avis/client', name: 'avisClient')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, AvisRepository $avisRepository): Response
    {
        $user = $security->getUser();
        $existingAvis = false;

        if ($user) {
            $existingAvis = $avisRepository->hasUserSubmittedAvis($user);
        }

        $avis = new Avis();

        if ($user && !$existingAvis) {
            $avis->setUser($user);
        }

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$existingAvis) {
            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été enregistré avec succès!');

            return $this->redirectToRoute('avisClient');
        }

        $avisList = $avisRepository->findBy([], ['createdAt' => 'DESC']);
        $averageRating = $avisRepository->findAverageRating();

        return $this->render('home/avis/avisClient.html.twig', [
            'form' => $form->createView(),
            'avisList' => $avisList,
            'averageRating' => $averageRating,
            'existingAvis' => $existingAvis
        ]);
    }

    // PROFILE USER 

    #[Route('/compte/{nom}', name: 'profile')]
    public function profile(Request $request, Security $security, string $nom, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        if (!$user || $user->getNom() !== $nom) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès');

            return $this->redirectToRoute('profile', ['nom' => $nom]);
        }

        return $this->render('home/profile/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    // PAGE DE VERIFICATION VALIDER 

    #[Route('/valider/compte', name: 'verification_accepter')]
    public function validation(): Response
    {   
        return $this->render('home/verification/tokenVerif.html.twig');
    }
}
