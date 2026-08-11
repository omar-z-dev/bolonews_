<?php

namespace App\Controller;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class AdminController extends AbstractController
{
    #[Route('/dashboardAdmin', name: 'app_admin')]
    public function indexAdmin (
        ArticleRepository $articleRepository,
        UserRepository $userRepository
        ): Response
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
        // On récupère les utilisateurs
        $utilisateurs = $userRepository->findAll();

        return $this->render('admin/dashboardAdmin.html.twig', [
            'articlesPublies'    => $articlesPublies,
            'articlesNonPublies' => $articlesNonPublies,
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/dashboardAdmin/utilisateur/{id}/supprimer',
    name: 'admin_user_supprimer',
    methods: ['POST']
    )]
    public function supprimerUtilisateur(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Vérification du token CSRF
        if ($this->isCsrfTokenValid(
            'delete-user-' . $user->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/dashboardAdmin/utilisateur/{id}/bannir',name: 'admin_user_bannir', methods: ['POST'])]
    public function bannirUtilisateur(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid(
            'bannir-user-' . $user->getId(),
            $request->request->get('_token')
        )) {
             $user->setIsBanned(!$user->isBanned());

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }
}
