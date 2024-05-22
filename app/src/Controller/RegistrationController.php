<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AuthentificationAuthenticator;
use App\Service\JwtService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        JwtService $jwt,
        SendMailService $mail
    ): Response {

        if ($this->getUser()) {
            $this->addFlash('danger', 'Vous êtes déjà connecté.');
            return $this->redirectToRoute('homepage');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // On genere le jwt de l'utilisateur
            // on cree le header
            $header = [
                'type' => 'JWT',
                'alg' => 'HS256',
            ];

            $payload = [
                'id' => $user->getId(),
            ];
            // on genere le token 
            $token = $jwt->gerenate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'jaysonmansuy@icloud.com',
                $user->getEmail(),
                "Inscription du site JM DEV",
                'registerEmail',
                compact('user', 'token')
            );


            return $security->login($user, AuthentificationAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser(
        $token,
        JwtService $jwt,
        UserRepository $usersRepository,
        EntityManagerInterface $em,
    ): Response {
        //On verifie si le token est valide n'a pas expiré & n'a pas été modifier
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // On recupére le payload

            $payload = $jwt->getPayload($token);

            //On recupere le user du token

            $user = $usersRepository->find($payload['id']);

            //On verifie que l'utilisateur existe et n'a pas encore activé son compte

            if ($user && !$user->getValidate()) {
                $user->setValidate(true);
                $em->flush($user);
                $this->addFlash('success', 'Félicitations le compte à bien été activez ! ');
                return $this->redirectToRoute('verification_accepter');
            }
        }
        // ici un problème se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou à expiré');
        return $this->redirectToRoute('verification_accepter');
    }

    #[Route('/newVerif', name: 'resend_verif')]
    public function resendVerif(
        JwtService $jwt,
        SendMailService $mail,
        UserRepository $usersRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accèder à cette page ');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getValidate()) {
            $this->addFlash('danger', 'Vous avez déjà verifié votre compte email ');
            return $this->redirectToRoute('app_login');
        }

        // On genere le jwt de l'utilisateur
        // on cree le header
        $header = [
            'type' => 'JWT',
            'alg' => 'HS256',
        ];

        $payload = [
            'id' => $user->getId(),
        ];
        // on genere le token 
        $token = $jwt->gerenate($header, $payload, $this->getParameter('app.jwtsecret'));


        // Envoyez envoie un mail
        $mail->send(
            'jaysonmansuy@icloud.com',
            $user->getEmail(),
            "Inscription sur JM DEV",
            'registerEmail',
            compact('user', 'token')
        );

        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('homepage');
    }
}
