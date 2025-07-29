<?php

namespace App\Twig\Runtime;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Twig\Extension\RuntimeExtensionInterface;

class CommentRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {
        // Inject dependencies if needed
    }

    public function getLastComment(): Comment|null
    {
        $comment = $this->commentRepository->findOneBy([], ['createdAt' => 'DESC']);
        return $comment;
    }
}
