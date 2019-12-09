<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    const EXCERPT_LENGTH = 150;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"User", "Post", "Posts"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=90)
     * @Groups({"User", "Posts", "Post"})
     * @Assert\Length(min="5", max="90", groups={"Create", "Update"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"User", "Posts", "Post"})
     * @Assert\Length(min="5", max="100", groups={"Create", "Update"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Groups({"Post"})
     * @Assert\NotBlank(groups={"Create", "Update"})
     */
    private $content;

    /**
     * @var string
     * @Groups({"Posts"})
     */
    private $excerpt = '';

    /**
     * @var array
     * @Groups({"Posts"})
     */
    private $count = [];

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"Post"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"Post"})
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Attachment",
     *     mappedBy="post",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true)
     * @Groups("Post")
     */
    private $attachments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", mappedBy="posts", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Post"})
     * @Assert\Valid(groups={"Create", "Update"})
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", mappedBy="posts", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     * @Groups("Post")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts", cascade={"persist"}, fetch="EAGER")
     * @Groups("Post")
     */
    private $user;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->categories  = new ArrayCollection();
        $this->tags        = new ArrayCollection();
    }

    /**
     * @ORM\PostLoad()
     */
    public function onPostLoad()
    {
        $excerpt = $this->getContent();
        if (strlen($this->getContent()) > Post::EXCERPT_LENGTH) {
            $excerpt = substr($this->getContent(), 0, Post::EXCERPT_LENGTH);
            $excerpt .= ' [...]';
        }

        $this->setExcerpt($excerpt);
        $this->addCount('categories', count($this->getCategories()));
        $this->addCount('tags', count($this->getTags()));
        $this->addCount('attachments', count($this->getAttachments()));
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime());
        if ( ! strlen($this->slug)) {
            $slugify = new Slugify();
            $this->setSlug($slugify->slugify($this->getTitle()));
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|Attachment[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        if ( ! $this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setPost($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getPost() === $this) {
                $attachment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if ( ! $this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addPost($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removePost($this);
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if ( ! $this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removePost($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    /**
     * @param string $excerpt
     *
     * @return Post
     */
    public function setExcerpt(string $excerpt): Post
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    /**
     * @return array
     */
    public function getCount(): array
    {
        return $this->count;
    }

    /**
     * @param array $count
     *
     * @return Post
     */
    public function setCount(array $count): Post
    {
        $this->count = $count;

        return $this;
    }

    public function addCount($key, $value)
    {
        $this->count[$key] = $value;
    }
}
