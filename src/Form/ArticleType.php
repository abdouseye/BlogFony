<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category', EntityType::class, [ // ici je dis que category est un champ de type EntityType et je lui donne un tableau d'option
                'class' => Category::class, // Lui dire de quel entité on parle
                'choice_label' => 'title' // je dis à mon champ qu'est ce qu'il doit présenter comme information ici un select liste qui choisi un titre
            ])
            ->add('content')
            ->add('image')
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
