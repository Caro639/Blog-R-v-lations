<?php

namespace App\Controller;

use App\Form\EditProfilFormType;
use App\Form\RegistrationForm;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ProfilController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ) {
        // Inject dependencies if needed
    }

    #[Route('/profil/{id}', name: 'app_profil')]
    /**
     * show user profile by ID
     * @param string $id
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(string $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $userId = $this->userRepository->findOneBy(['id' => $id]);

        return $this->render('profil/index.html.twig', [
            'user' => $userId,
        ]);
    }

    #[Route('/profil/post/{id}', name: 'app_profil_post')]
    /**
     * show posts of owner user
     * @param string $id
     * @param \App\Repository\PostRepository $postRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function postUser(string $id, PostRepository $postRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $userId = $this->userRepository->findOneBy(['id' => $id]);

        $posts = $postRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        dump($posts);

        return $this->render('profil/post.html.twig', [
            'user' => $userId,
            'posts' => $posts,
        ]);
    }

    #[Route('/profil/edit/{id}', name: 'app_profil_edit')]
    /**
     * modifier son profil
     * @param string $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $userPasswordHasher
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(
        string $id,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $userPasswordHasher,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->userRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(EditProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // /** @var string $plainPassword */
            // $plainPassword = $form->get('plainPassword')->getData();

            // // encode the plain password
            // $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setPhoto($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été modifié avec succés !');

            return $this->redirectToRoute('app_profil', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('profil/edit.html.twig', [
            'editProfilForm' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/profil/comment/{id}', name: 'app_profil_comment')]
    /**
     * Summary of showCommentUser
     * @param string $id
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Repository\CommentRepository $commentRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function showCommentUser(
        string $id,
        PostRepository $postRepository,
        CommentRepository $commentRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $userId = $this->userRepository->findOneBy(['id' => $id]);

        $posts = $postRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        dump($posts);

        $comments = $commentRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        dump($comments);

        return $this->render('profil/comment.html.twig', [
            'user' => $userId,
            'posts' => $posts,
            'comments' => $comments,
        ]);
    }
}
