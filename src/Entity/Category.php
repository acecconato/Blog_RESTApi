<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Post", "Categories", "Category"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(groups={"Create", "Update"})
     * @Assert\Length(max="20", groups={"Create", "Update"})
     * @Groups({"Post", "Categories", "Category"})
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\Length(min="4", max="20", groups={"Create", "Update", "Categories", "Category"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"Category"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", inversedBy="categories")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Category"})
     */
    private $posts;

    /**
     * @var int
     * @Groups({"Categories"})
     */
    private $postsCount = 0;

    /**
     * @ORM\PostLoad()
     */
    public function onPostLoad()
    {
        $this->setPostsCount(count($this->getPosts()));
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        if ( ! $this->getSlug()) {
            $slugify = new Slugify();
            $this->setSlug($slugify->slugify($this->getLabel()));
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if ( ! $this->posts->contains($post)) {
            $this->posts[] = $post;
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getPostsCount(): int
    {
        return $this->postsCount;
    }

    /**
     * @param int $postsCount
     *
     * @return Category
     */
    public function setPostsCount(int $postsCount): Category
    {
        $this->postsCount = $postsCount;

        return $this;
    }
}
