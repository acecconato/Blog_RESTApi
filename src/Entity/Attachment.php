<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttachmentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Attachment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="attachments")
     */
    private $post;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $tempFilename;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $abs_path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rel_path;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime();
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getTempFilename(): ?string
    {
        return $this->tempFilename;
    }

    public function setTempFilename(string $tempFilename): self
    {
        $this->tempFilename = $tempFilename;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAbsPath(): ?string
    {
        return $this->abs_path;
    }

    public function setAbsPath(string $abs_path): self
    {
        $this->abs_path = $abs_path;

        return $this;
    }

    public function getRelPath(): ?string
    {
        return $this->rel_path;
    }

    public function setRelPath(string $rel_path): self
    {
        $this->rel_path = $rel_path;

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

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
