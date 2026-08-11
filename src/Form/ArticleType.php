<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Saisissez le titre de l’article',
                ],
            ])

            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Rédigez votre article',
                ],
            ])

            // Champ temporaire pour choisir un fichier
            ->add('imageFile', FileType::class, [
                'label' => 'Image de l’article',

                // imageFile n’existe pas dans l’entité Article
                'mapped' => false,

                // iserer une image lors de la modif n'est pas oblifatoire
                'required' => false,

                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'maxSizeMessage' =>
                            'L’image ne doit pas dépasser 2 Mo.',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' =>
                            'Veuillez choisir une image JPG, PNG ou WebP.',
                    ]),
                ],
            ])

            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,

                'choice_label' => function (
                    Categorie $categorie
                ): string {
                    return '- ' . $categorie->getLibelle();
                },

                'placeholder' => 'Choisir une catégorie',

                'placeholder_attr' => [
                    'disabled' => true,
                ],
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}