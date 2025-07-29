<?php

namespace App\Twig\Runtime;

use App\Entity\Post;
use App\Repository\PostRepository;
use Twig\Extension\RuntimeExtensionInterface;

class PostRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private PostRepository $postRepository
    ) {
        // Inject dependencies if needed
    }

    public function getLastPost(): Post|null
    {
        $post = $this->postRepository->findOneBy([], ['createdAt' => 'DESC']);
        return $post;
    }
}
