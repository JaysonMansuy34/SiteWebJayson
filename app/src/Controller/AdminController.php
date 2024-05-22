<?php

namespace App\Controller;

use App\Form\AdminTypeUser;
use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/dashboard', name: 'dashboard')]
    public function dashboard(UserRepository $userRepository, AvisRepository $avisRepository, Request $request): Response
    {
        $users = $userRepository->findAll();
        $userCount = $userRepository->countUsers();
        $lastThreeAvis = $avisRepository->findLastThreeAvis();

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'userCount' => $userCount,
            'lastThreeAvis' => $lastThreeAvis
        ]);
    }


    #[Route('/admin/users', name: 'admin_user')]
    public function users(UserRepository $userRepository, request $request): Response
    {

        $users = $userRepository->findAll();

        return $this->render('admin/user.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'admin_user_edit')]
    public function usersEdit(
        UserRepository $userRepository,
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Il y a pas ce nom ' . $id);
        }

        $form = $this->createForm(AdminTypeUser::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger après l'édition
            return $this->redirectToRoute('admin_user');
        }

        return $this->render('admin/userEdit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/{id}/delete', name: 'admin_user_delete')]
    public function deleteUser(UserRepository $userRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé pour id ' . $id);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_user');
    }
}
