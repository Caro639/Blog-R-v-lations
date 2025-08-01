<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $isReply = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Comment $comment = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'comment')]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'like_comment')]
    private Collection $like_comment;

    // /**
    //  * @var Collection<int, User>
    //  */
    // #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'dislikes_user')]
    // private Collection $dislikes;

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isReply = false; // Default value for isReply
        $this->comments = new ArrayCollection();
        $this->like_comment = new ArrayCollection();
        // $this->dislikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isReply(): ?bool
    {
        return $this->isReply;
    }

    public function setIsReply(bool $isReply): static
    {
        $this->isReply = $isReply;

        return $this;
    }

    public function getComment(): ?self
    {
        return $this->comment;
    }

    public function setComment(?self $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setComment($this);
        }

        return $this;
    }

    public function removeComment(self $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getComment() === $this) {
                $comment->setComment(null);
            }
        }

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPost(): ?post
    {
        return $this->post;
    }

    public function setPost(?post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikeComment(): Collection
    {
        return $this->like_comment;
    }

    public function addLikeComment(User $likeComment): static
    {
        if (!$this->like_comment->contains($likeComment)) {
            $this->like_comment[] = $likeComment;
        }

        return $this;
    }

    public function isLikeCommentedByUser(User $user): bool
    {
        return $this->like_comment->contains($user);
    }

    /**
     * get the number of likes for the post
     * @return int
     */
    public function howManyLikeComment(): int
    {
        return count($this->like_comment);
    }

    public function removeLikeComment(User $likeComment): static
    {
        $this->like_comment->removeElement($likeComment);

        return $this;
    }
}
