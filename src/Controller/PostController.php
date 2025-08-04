<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostForm;
use App\Form\CommentForm;
use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/post')]
final class PostController extends AbstractController
{
    // todo page dashboard private for ADMIN + voter getOwner
    /**
     * show all posts for all users todo by categories tableau de posts à modifier
     *
     * @param PostRepository $postRepository
     * @return Response
     */
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * add a new post
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $post = new Post();
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setUser($user);

            // Generate slug from title
            $slug = strtolower($slugger->slug($post->getTitle()));
            $post->setSlug($slug);

            $image = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setImage($newFilename);
            }

            // dd($post);


            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Votre article a été ajouté avec succés !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'postForm' => $form,
        ]);
    }

    // todo slugger
    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    /**
     * Show a single post with its comments and categories for all users
     * @param \App\Repository\CategorieRepository $categorieRepository
     * @param \App\Repository\CommentRepository $commentRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Entity\Post $post
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function show(
        CategorieRepository $categorieRepository,
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        Post $post
    ): Response {
        // Récupérer toutes les catégories parentes pour le widget sidebar
        $categories = $categorieRepository->findBy(['parent' => null], ['name' => 'ASC']);

        // Afficher les 4 derniers commentaires
        $comments = $commentRepository->findBy(['post' => $post], ['createdAt' => 'DESC'], 4);

        // Formulaire de commentaire (seulement pour les utilisateurs connectés)
        $comment = new Comment();
        $form = null;

        if ($this->getUser()) {
            $form = $this->createForm(CommentForm::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setUser($this->getUser());
                $comment->setPost($post);

                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', 'Votre commentaire a été ajouté avec succès !');

                return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
                // return $this->redirectToRoute($request->attributes->get('_route'));
            }
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'categories' => $categories,
            'comments' => $comments,
            'commentForm' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_post_edit', methods: ['GET', 'POST'])]
    /**
     * Edit an existing post by the author
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Post $post
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        // Ensure the post belongs to the current user
        if ($post->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cet article.');
            return $this->redirectToRoute('app_post_index');
        }

        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Generate slug from title
            $slug = strtolower($slugger->slug($post->getTitle()));
            $post->setSlug($slug);

            $image = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_profil_post', ['id' => $user], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'postForm' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_post_delete', methods: ['POST'])]
    /**
     * Delete a post by the author
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Post $post
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $currentUser = $this->getUser();

        // Ensure the post belongs to the current user
        if ($post->getUser() !== $currentUser) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cet article.');
            return $this->redirectToRoute('app_profil', ['id' => $currentUser], Response::HTTP_SEE_OTHER);
        }

        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($post);
                $entityManager->flush();

                $this->addFlash('success', 'L\'article a été supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression de l\'article.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_profil_post', ['id' => $currentUser], Response::HTTP_SEE_OTHER);
    }
}
