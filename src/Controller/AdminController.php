<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * Méthode pour lister les utilisateurs du blog
     *  
     * @Route("/utilisateurs", name="utilisateurs")
     * 
     */

    public function usersList(UserRepository $users)
    {

        return $this->render("admin/users.html.twig", [
            'users' => $users->findAll()
        ]);
    }

    /**
     * Méthode pour modifier un utilisateur (on aura besoin d'un fichier twig, un formulaire et la méthode elle même)
     * 
     * @Route("/utilisateur/modifier/{id}", name="modifier_utilisateur")
     */

    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succés');
            return $this->redirectToRoute('admin_utilisateurs');
        }

        return $this->render('admin/edituser.html.twig', [
            'userForm' => $form->createView()
        ]);
    }
}
