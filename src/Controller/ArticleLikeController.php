<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleLike;
use App\Repository\ArticleLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleLikeController extends AbstractController
{
    #[Route('/article/{id}/like', name: 'article_like', methods: ['POST'])]
    public function like(
        Article $article,
        ArticleLikeRepository $articleLikeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        // L'utilisateur doit être connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        // L'utilisateur ne peut pas liker son propre article
        if ($article->getAuteur() === $user) {
            return $this->json([
                'error' => 'Vous ne pouvez pas liker votre propre article.'
            ], 403);
        }

        // Vérifie si l'utilisateur a déjà liké l'article
        $like = $articleLikeRepository->findOneBy([
            'article' => $article,
            'utilisateur' => $user,
        ]);

        if ($like) {

            // Déjà liké : on retire le like
            $entityManager->remove($like);
            // variable utilse ds la fonction js pour mise a jour de la couelur du coeur 
            $liked = false;

        } else {

            // Pas encore liké : on crée un nouveau like
            $like = new ArticleLike();

            $like->setArticle($article);
            $like->setUtilisateur($user);
            $like->setDateCreation(new \DateTimeImmutable());

            $entityManager->persist($like);

            $liked = true;
        }

        $entityManager->flush();

        return $this->json([
            'liked' => $liked,
            'nombreLikes' => $article->getArticleLikes()->count(),
        ]);
        // exemple de retour :
        /*

        {
            "liked": false,
            "nombreLikes": 7
        }
            
        */
    }
}