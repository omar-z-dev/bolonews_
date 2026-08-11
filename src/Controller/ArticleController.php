<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CategorieRepository;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'article_liste')]
    public function liste(
        Request $request,
        ArticleRepository $articleRepository,
        CategorieRepository $categorieRepository
    ): Response
    {
        //recuperer le texte de recherche ou la categorie cliqué (id)
        $recherche = $request->query->get('search');

        $categorieId = $request->query->get('categorie');

        //toutes les categories
        $categories = $categorieRepository->findAll();

        // cas possiblede la recherche
        if ($recherche) {
        $articles = $articleRepository->rechercher($recherche);
        } elseif ($categorieId) {
            $articles = $articleRepository->findBy([
                'categorie' => $categorieId,
                'publie' => true,
            ]);
        } else {
            $articles = $articleRepository->findBy([
                'publie' => true,
            ]);
        }

        return $this->render('article/articles.html.twig', [
            'articles'    => $articles,
            'categories'  => $categories,
            'recherche'   => $recherche,
            'categorieId' => $categorieId,
            ]);
    }
    /*=================================
                  ajouter
    ===================================*/

    #[Route('/article/ajouter', name: 'article_ajouter')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        // Seul un utilisateur connecté peut créer un article
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Création d’un article vide
        $article = new Article();

        // Association automatique de l’auteur connecté au nouvel article
        $article->setAuteur($this->getUser());
        // Valeurs par défaut
        $article->setDateCreation(new \DateTimeImmutable());
        $article->setPublie(false);

        // Création du formulaire
        $form = $this->createForm(ArticleType::class, $article);
        // Récupération des données envoyées
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             /** @var UploadedFile|null $imageFile */
            $imageFile = $form
                ->get('imageFile')
                ->getData();

            if ($imageFile !== null) {
                $nomOriginal = pathinfo(
                    $imageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                // Nettoie le nom du fichier
                $nomSecurise = $slugger->slug($nomOriginal);
                // Exemple :
                // mon-article-68f4c1a98d.jpg
                $nouveauNom = $nomSecurise
                    . '-'
                    . uniqid()
                    . '.'
                    . $imageFile->guessExtension();
                // Dossier réel du projet
                $dossier = $this->getParameter('articles_images_directory');

                // Déplace réellement le fichier
                $imageFile->move(
                    $dossier,
                    $nouveauNom
                );

                // Enregistre uniquement le nom en base
                $article->setImage($nouveauNom);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('article/ajouter.html.twig', [
            'form' => $form,
        ]);
    }
    /*=================================
                 modifier
    ===================================*/
    #[Route('/article/{id}/modifier', name: 'article_modifier')]
    public function modifier(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        // Seul un utilisateur connecté peut modifier
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérifie que l'article appartient bien à l'utilisateur connecté
        if ($article->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        // On garde le nom de l'ancienne image
        $ancienneImage = $article->getImage();

        // Le formulaire est prérempli avec l'article existant
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */

            //récupèrer le fichier que l'utilisateur a sélectionné.
            $imageFile = $form->get('imageFile')->getData();

            // Si l'utilisateur a choisi une nouvelle image
            if ($imageFile !== null) {
                
                //récupérer le nom du fichier sans son extension.
                $nomOriginal = pathinfo(
                    $imageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $nomSecurise = $slugger->slug($nomOriginal);
                $nouveauNom = $nomSecurise
                    . '-'
                    . uniqid()
                    . '.'
                    . $imageFile->guessExtension();
                $dossier = $this->getParameter('articles_images_directory');
                $imageFile->move(
                    $dossier,
                    $nouveauNom
                );
                $article->setImage($nouveauNom);
                // Supprime l'ancienne image
                if ($ancienneImage !== null) {
                    $ancienChemin = $dossier . '/' . $ancienneImage;

                    if (is_file($ancienChemin)) {
                        unlink($ancienChemin);
                    }
                }
            }

            // Date automatique de modification
            $article->setDateModification(new \DateTimeImmutable());
            // Pas besoin de persist(), car l'article existe déjà
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('article/modifier.html.twig', [
            'form' => $form,
            'article' => $article,
        ]);
    }

    /*=================================
                 publier
    ===================================*/
    #[Route( '/article/{id}/publier',name: 'article_publier',methods: ['POST'])]
    public function publier(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        // Refuser l'accès si l'utilisateur n'a pas le rôle ROLE_USER.
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérifie que l'article appartient bien à l'utilisateur connecté Cela arrête immédiatement l'exécution et Symfony renvoie une erreur 403 - Accès refusé.
        if ($article->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Vérifie le token CSRF envoyé par le formulaire
        if (!$this->isCsrfTokenValid(
            'publier' . $article->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Token CSRF invalide.'
            );
        }

        // Mettre l'article en ligne : publie de false vers true
        $article->setPublie(true);

        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

     /*=================================
                 depublier
    ===================================*/
    #[Route( '/article/{id}/depublier',name: 'article_depublier',methods: ['POST'])]
    public function depublier(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        // Refuser l'accès si l'utilisateur n'a pas le rôle ROLE_USER.
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérifie que l'article appartient bien à l'utilisateur connecté Cela arrête immédiatement l'exécution et Symfony renvoie une erreur 403 - Accès refusé.
        if ($article->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Vérifie le token CSRF envoyé par le formulaire
        if (!$this->isCsrfTokenValid(
            'depublier' . $article->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Token CSRF invalide.'
            );
        }

        // Mettre l'article en ligne : publie de false vers true
        $article->setPublie(false);

        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
     /*=================================
                 supprimer
    ===================================*/
    #[Route('/article/{id}/supprimer', name: 'article_supprimer', methods: ['POST'])]
    public function supprimer(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérifie que l'article appartient à l'utilisateur connecté
        if ($article->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Vérifie le token CSRF :vérifier que le token reçu est bien celui qu'il avait généré.
        if (!$this->isCsrfTokenValid(
            'supprimer' . $article->getId(),
            $request->request->get('_token')
        )) {
            //afficher 403 Access Denied
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    /*=================================
                 afficher
    ===================================*/
    #[Route('/article/{id}', name: 'article_afficher')]
    public function afficher(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    { 
        //dd($article->getArticleLikes());
        //dd($article->getArticleLikes()->toArray());
        
        // Création d'un nouveau commentaire vide
        $commentaire = new Commentaire();

        // Création du formulaire
        $form = $this->createForm(CommentaireType::class, $commentaire);

        // Récupération des données envoyées
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Interdire de commenter son propre article
            if ($article->getAuteur() === $this->getUser()) {

                $this->addFlash(
                    'danger',
                    'Vous ne pouvez pas commenter votre propre article.'
                );

                return $this->redirectToRoute('article_afficher', [
                    'id' => $article->getId(),
                ]);
            }

            // Associer le commentaire à l'article
            $commentaire->setArticle($article);

            // Associer l'utilisateur connecté
            $commentaire->setAuteur($this->getUser());

            // Date de création
            $commentaire->setDatePublication(new \DateTimeImmutable());

            //verifier si comm existe deja
            $commentaireExistant = $entityManager
                ->getRepository(Commentaire::class)
                ->findOneBy([
                    'article' => $article,
                    'auteur' => $this->getUser(),
                ]);
            if ($commentaireExistant) {

                $this->addFlash(
                    'danger',
                    'Vous avez déjà commenté cet article.'
                );

                return $this->redirectToRoute('article_afficher', [
                    'id' => $article->getId(),
                ]);
            }

            // Enregistrer le commentaire
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('article_afficher', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/afficher.html.twig', [
            'article' => $article,
            'formCommentaire' => $form,
        ]);
    }
}