<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinksRepository")
 */
class Links
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $idLinks;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $original_link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $short_link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function getIdLinks(): ?int
    {
        return $this->idLinks;
    }

    public function getOriginalLink(): ?string
    {
        return $this->original_link;
    }

    public function setOriginalLink(string $original_link): self
    {
        $this->original_link = $original_link;

        return $this;
    }

    public function getShortLink(): ?string
    {
        return $this->short_link;
    }

    public function setShortLink(string $short_link): self
    {
        $this->short_link = $short_link;

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

}
