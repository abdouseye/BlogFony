<?php

namespace App\Controller;


use DateTime;

use App\Entity\Comment;

use App\Entity\Articles;
use App\Form\ArticleType;
use App\Form\CommentType;

use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Driver\SQLSrv\LastInsertId;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController

{


    /**
     * @Route("/", name="home")
     */
    public function home(ArticlesRepository $repo)
    {

    $articles = $repo->findBy([], ['id'=>'DESC'], 6);

        return $this->render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticlesRepository $repo)
    {
        // Je crée une variable repo et je vais dire à mon controller de demander à doctrine de me donner le repository de la classe Articles

        // $repo = $this->getDoctrine()->getRepository(Articles::class);

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    // création d'une fonction qui permet de créer un formulaire

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */

    public function form(Articles $article = null, Request $request, EntityManagerInterface $manager)
    { // j'utilise le paramétre converter en demandant à symfony de nous passé un artcile qu'on va appeler $article et je lui dis que parfois cette article pourrait ^tre null pour afficher le formulaire vide , s'il reçoit $article il affiche un formulaire pour une mise à jour de l'article

        if (!$article) { // si je n'ai pas d'article , je veux créer un nouveau article en instanciant un new articles

            $article = new Articles(); // instanciation d'un nouvel article 

        }
        // création du formulaire lié à mon article (binder) et le configurer en lui donnant les champs que l'on veut y ajouter

        $form = $this->createForm(ArticleType::class, $article); // en paramétre le nom de la class du formulaire et l'entité auquel je veux le binder
        $form->handleRequest($request); // analyse de la requete
        if ($form->isSubmitted() && $form->isValid()) { // vérification du submit et de la validité des données saisies

            if (!$article->getId()) { // Si l'article n'a pas d'identifiant donc n'existe pas . donc je vais mettre une date
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article); // Je fais persister les données saisies 
            $manager->flush(); //  je les enregistres

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]); // et je retoure vesr la page qui me montre le nouveau article en recupérant son ID
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }
    // création d'une fonction pour afficher un article

    /**
     * @Route("/blog/{id}" , name= "blog_show")
     */

    public function show(Articles $article, Request $request, EntityManagerInterface $manager)
    {

        $comment = new Comment(); // On initiialise un commentaire

        $form = $this->createForm(CommentType::class, $comment); // On crée un formulaire 

        $form->handleRequest($request); // on demande au formulaire de gérer request que je te passe

        if ($form->isSubmitted() && $form->isValid()) {

            // expliquer au commentaire qu'il vient d'être créer et a qu'el article il est lié

            $comment->setCreatedAt(new \DateTime()) // je récupére la date du jour
                ->setArticle($article); // je recupére l'artcle au quel sera lié le commentaire

            $manager->persist($comment);
            $manager->flush();

            // je crée le retour vers la page de l'artcle identifier par son id

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        // $repo = $this->getDoctrine()->getRepository(Articles::class);

        // $article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView() // Je veux la partie affichable de mon formulaire
        ]);
    }
}
