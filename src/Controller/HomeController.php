<?php

namespace App\Controller;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

 /*==== PAGE ACCUEIL ====*/
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // On récupère les 5 derniers articles publiéss
        $articles = $articleRepository->findBy(
            ['publie' => true],
            ['dateCreation' => 'DESC'],
            5
        );

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }


    /*==== LISTE DES ARTICLES ====
    #[Route('/articles', name: 'article_liste')]
    public function liste(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy(
            ['publie' => true],
            ['dateCreation' => 'DESC'],
            6
        );

        return $this->render('article/articles.html.twig', [
            'articles' => $articles,
        ]);
    }*/
}
