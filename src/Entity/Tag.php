<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Post", "Tags", "Tag"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"Post", "Tags", "Tag"})
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"Tags", "Tag"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"Tag"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", inversedBy="tags")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Tag"})
     */
    private $posts;

    /**
     * @var int
     * @Groups({"Tags"})
     */
    private $countPosts = 0;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @ORM\PostLoad()
     */
    public function onPostLoad()
    {
        $this->setCountPosts($this->getPosts()->count());
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
     * @param int $countPosts
     *
     * @return Tag
     */
    public function setCountPosts(int $countPosts): Tag
    {
        $this->countPosts = $countPosts;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountPosts(): int
    {
        return $this->countPosts;
    }
}
