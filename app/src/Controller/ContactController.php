<?php 

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form);
            // Enregistrer le contact dans la base de données
            $entityManager->persist($contact);
            $entityManager->flush();

            // Redirection ou affichage d'un message de succès
            return $this->redirectToRoute('contact');
        }

        return $this->render('models/accueil/formulaireContact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
