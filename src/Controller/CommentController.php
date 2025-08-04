<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentForm;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    #[Route(name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    /**
     * add a new comment to a post
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\PostRepository $postRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PostRepository $postRepository,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();


        $post = $postRepository->find($request->attributes->get('id'));


        $comment = new Comment();
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUser($user);
            $comment->setPost($post);

            // dd($comment);
            // dd($user);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'commentForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {

        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    /**
     * this method allows a user to edit their comment.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Comment $comment
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\PostRepository $postRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(
        Request $request,
        Comment $comment,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user !== $comment->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce commentaire.');
            return $this->redirectToRoute('app_profil_comment', ['id' => $user], Response::HTTP_SEE_OTHER);
        }

        // Récupérer le post depuis le commentaire existant
        $post = $comment->getPost();

        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pas besoin de réassigner le user et le post car ils n'ont pas changé
            // $comment->setUser($user);
            // $comment->setPost($post);

            $entityManager->flush();

            $this->addFlash('success', 'Commentaire modifié avec succès.');

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'commentForm' => $form,
        ]);
    }

    // todo delete button add to comment
    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user !== $comment->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce commentaire.');
            return $this->redirectToRoute('app_profil_comment', ['id' => $user], Response::HTTP_SEE_OTHER);
        }

        $post = $comment->getPost();

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Commentaire supprimé avec succès.');
        // return $this->redirectToRoute('app_profil_comment', ['id' => $user], Response::HTTP_SEE_OTHER);
        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }
}
