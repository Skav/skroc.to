<?php


namespace App\DTO;


class LinksDTO
{
    private $id_links;
    private $original_link;
    private $short_link;
    private $slug;

    public function __construct($id, $original_link, $short_link, $slug)
    {
        $this->id_links = $id;
        $this->original_link = $original_link;
        $this->short_link = $short_link;
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getIdLinks()
    {
        return $this->id_links;
    }

    /**
     * @return mixed
     */
    public function getOriginalLink()
    {
        return $this->original_link;
    }

    /**
     * @return mixed
     */
    public function getShortLink()
    {
        return $this->short_link;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }
}