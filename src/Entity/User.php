<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"List", "User", "Post"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     * @Groups({"List", "User", "Post"})
     * @Assert\NotBlank(groups={"Create"})
     * @Assert\Length(min="4", max="40", groups={"Create", "Update"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"List", "User"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"Password"})
     * @Assert\NotBlank(groups={"Create"})
     * @Assert\Length(min="6", max="50", groups={"Create", "Update"})
     * @Assert\NotCompromisedPassword(groups={"Create", "Update"})
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"User"})
     */
    private $posts;

    /**
     * @var integer
     * @Groups({"List", "Post"})
     */
    private $postsCount;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @ORM\PostLoad()
     */
    public function onPostLoad()
    {
        $this->postsCount = (int)count($this->getPosts());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
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
     * @return User
     */
    public function setPostsCount(int $postsCount): User
    {
        $this->postsCount = $postsCount;

        return $this;
    }
}
