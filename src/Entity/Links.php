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
    private $orginal_link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $short_link;

    public function getIdLinks(): ?int
    {
        return $this->idLinks;
    }

    public function getOrginalLink(): ?string
    {
        return $this->orginal_link;
    }

    public function setOrginalLink(string $orginal_link): self
    {
        $this->orginal_link = $orginal_link;

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
}
