<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LikeController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_like')]
    public function index(Post $post, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        if ($post->isLikedByUser($user)) {
            $post->removeLike($user);
            $manager->flush();

            return $this->json([
                'nbLike' => $post->howManyLikes(),
                'message' => 'Like removed',
            ]);
        }
        // else {
        $post->addLike($user);
        $manager->flush();

        return $this->json([
            'nbLike' => $post->howManyLikes(),
            'message' => 'Post liked'
        ]);
    }
}
