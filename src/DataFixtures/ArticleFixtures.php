<?php

namespace App\DataFixtures;


use App\Entity\Comment;
use App\Entity\Articles;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // je crée  une variable $faker que j'instancie avec la classe Faker . J'appel dans l'espace de nom faker la class Factory via une méthide statique create() en lui passant une locale (FR'_fr') pour dire que les données seront en français
        
        $faker = \Faker\Factory::create('fr_FR');

        // Creer 3 catégories fakées

        for($i = 1; $i <= 3; $i++){
            $category = new Category(); // Je crée une variable category  qui sera une nouvelle Category
            $category->setTitle($faker->sentence()) // je remplie en disant de set un title et de dire à faker de le générer lui même en utilisant la méthode sentence de faker 
                     ->setDescription($faker->paragraph());


                     $manager->persist($category);

                     // Créer entre 4 et  6 articles

            
        for($j = 1; $j <= mt_rand(4, 6); $j++){ // de j = 1 jusqu'a j = 10
            $article = new Articles(); // crée moi un article 

            $content = '<p>' .join($faker->paragraphs(5), '</p><p>') . '</p>'; // je crée une variable $content qui sera égale à un début de paragraphe. Ensuite dans paragraphe je rajoute un $faker->paragraphs(5)

            $article->setTitle($faker->sentence()) 
                    ->setContent($content) // $faker->paragraphs(5) donne une ensemble de paragraphe mais dans un tableau alors que mon setContent attend une chaine de caractére // ensuite je récupére la variable $content dans mon setContent
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category); // Dire dans quelle catégorie se place l'article

            $manager->persist($article); // je demande au manager à faire persister mon artcle dans le temp


            for($k = 1; $k <= mt_rand(4, 10); $k++){

                // On donne des commentaires à l'article

                $comment = new Comment();

                $content = '<p>' .join($faker->paragraphs(2), '</p><p>') . '</p>'; // je crée une variable $content qui sera égale à un début de paragraphe. Ensuite dans paragraphe je rajoute un $faker->paragraphs(5)

                $days = (new \DateTime())->diff($article->getCreatedAt())->days; // On crée un nouveau dateTime On fait la différence avec la date de ccréation de l'article pour en extraire les jours
                $comment->setAuthor($faker->name())
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                        ->setArticle($article); // Dire  à quel article appartient le commentaire

                        $manager->persist($comment); // Je demande au manager de faire persister le commentaire


            }
        }

        }

        $manager->flush();// balance la requéte sql qui mettra en place mes manipulations
    }
}
