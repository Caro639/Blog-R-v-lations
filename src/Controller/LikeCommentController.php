<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LikeCommentController extends AbstractController
{
    #[Route('/like/comment/{id}', name: 'app_like_comment')]
    public function indexComment(Comment $comment, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        if ($comment->isLikeCommentedByUser($user)) {
            $comment->removeLikeComment($user);

            $manager->flush();

            return $this->json([
                'nbLikeComment' => $comment->howManyLikeComment(),
                'message' => 'Like removed',
            ]);
        }

        $comment->addLikeComment($user);
        $manager->flush();

        return $this->json([
            'nbLikeComment' => $comment->howManyLikeComment(),
            'message' => 'Comment liked'
        ]);
    }
}
