<?php

namespace App\Controller;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index (ArticleRepository $articleRepository): Response
    {
        // On récupère  articles publiéss
        $articlesPublies = $articleRepository->findBy(
            [
                'publie' => true,
                'auteur' => $this->getUser(),
            ],
            ['dateCreation' => 'DESC']
        );
        // On récupère  articles non publiés
        $articlesNonPublies = $articleRepository->findBy(
            [
                'publie' => false,
                'auteur' => $this->getUser(),
            ],
            ['dateCreation' => 'DESC']
        );


        return $this->render('dashboard/dashboard.html.twig', [
            'articlesPublies'    => $articlesPublies,
            'articlesNonPublies' => $articlesNonPublies,
        ]);
    }
}
