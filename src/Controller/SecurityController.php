<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) // Ici la fonction registration à besoin de la requête HTTP pour pouvoir l'analyser et l'object manager de doctrine qui va nous permettre d'enregistrer l'utilisateur en base
    {



        // Dire à quel objet on relie les champs du formulaire

        $user = new User();

        // instanciation de notre formation

        $form = $this->createForm(RegistrationType::class, $user); // $user comme deuxieme paramétre de notre méthode veut dir que l'on relie les champs du formulaire à notre entité user
        $form->handleRequest($request); // Demander au formulaire d'analyser la request quelle reçoit en paramétre



        if ($form->isSubmitted() && $form->isValid()) {   // vérifier si tout est sousmis et que les champs du formulaire sont valide

            $hash = $encoder->encodePassword($user, $user->getPassword()); // avant de sauvegarder l'utilisateur, je dis que je veux un hash qui sera égal à mon encoder à qui je vais demander d'encoder un password de $user en lui spécifiant l'endroit ou se trouve le password
            $user->setPassword($hash); // Ici on demande de set le password de user et de le remplacer par $hash
            $manager->persist($user);  // Demander au formulaire de se préparer à sauvegarder $user passer en paramétre de createForm()
            $manager->flush(); // Sauvegarder réellement

            // Faire une redirection vers la page de connexion aprés enregistrement en lui donnant le nom de la route que je veux afficher

            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }


    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }
}
