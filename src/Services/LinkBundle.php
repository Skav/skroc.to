<?php

namespace App\Services;

use App\DTO\LinksDTO;
use App\Entity\Links;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class LinkBundle
{
    /**TODO
     * Add caching
     * Add unit tests
     * Replace Exceptions to HttpExceptions methods or write own exceptions (e.g 508 in generating slug)
     * Check is link empty
    **/

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getListOfLinks(): array
    {
        try {
            $data = (array) $this->entityManager->getRepository(Links::class)->findAll();
        }
        catch(\Exception $e) {
            throw $e;
        }
        return $data;
    }

    /**
     * @param $slug
     * @return object
     * @throws \Exception
     */
    public function getElementBySlug($slug): object
    {
        try {
            $repo = $this->entityManager->getRepository(Links::class);
            $data = $repo->findOneBy(['slug' => $slug]);
        }
        catch(\Exception $e){
            throw $e;
        }
        return new LinksDTO(
            $data->getIdLinks(),
            $data->getOriginalLink(),
            $data->getShortLink(),
            $data->getSlug()
        );
    }

    /**
     * @param $original_link
     * @return object
     * @throws \Exception
     */
    public function getExistedShortLink($original_link): object
    {
        try {
        $repo = $this->entityManager->getRepository(Links::class);
        $data = $repo->findOneBy(['original_link' => $original_link]);
    }
        catch(\Exception $e){
        throw $e;
    }

        return new LinksDTO(
            $data->getIdLinks(),
            $data->getOriginalLink(),
            $data->getShortLink(),
            $data->getSlug()
        );
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getUniqueSlug(): string
    {
        try {
            $i = 0;
            do {
                $slug = $this->generateRandomSlug();
                $i++;
            } while ($this->checkIsSlugExist($slug));
        }
        catch(\Exception $e){
            throw $e;
        }

        if($i >= 10)
            throw new \Exception('Infinite loop: Cannot find free slug');

        return $slug;
    }

    /**
     * @param $original_link
     * @return bool
     */
    public function checkIsLinkInDataBase($original_link): bool
    {
        $isDot = $this->checkLinkHaveDot($original_link);

        if(!$isDot)
            throw new AccessDeniedException('Invalid link');


        $repo = $this->entityManager->getRepository(Links::class);

        return ($repo->findOneBy(['original_link' => $original_link]) == null)? false : true;
    }

    /**
     * @param $slug
     * @param string $link
     * @return string
     * @throws \Exception
     */
    public function createNewLink($slug, $link='www.skroc.to/'): string
    {
        if(empty($slug) || empty($link))
            throw new \Exception('Empty value of link/slug');

        return $link.$slug;
    }

    /**
     * @param $original_link
     * @param $short_link
     * @param $slug
     * @return object
     * @throws \Exception
     */
    public function addLinkToDatabase($original_link, $short_link, $slug): object
    {
        try {
            $dataToValidate = array($original_link, $short_link, $slug);

            $this->validation($dataToValidate);

            $link = new Links();
            $link->setOriginalLink($original_link);
            $link->setSlug($slug);
            $link->setShortLink($short_link);

            $this->entityManager->persist($link);
            $this->entityManager->flush();
        }
        catch(\Exception $e){
            throw $e;
        }


        return new LinksDTO(
            $link->getIdLinks(),
            $link->getOriginalLink(),
            $link->getShortLink(),
            $link->getSlug()
        );
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    protected function validation($data = array()): bool
    {
        $wrongData = null;
        foreach($data as $index=>$item)
        {
            if(empty($item))
                $wrongData = $wrongData .$index. ", ";
        }

        if($wrongData != null)
            throw new \Exception('The '. $wrongData.' argument(s) empty');

        return true;
    }

    /**
     * @param $original_link
     * @return bool
     */
    protected function checkLinkHaveDot($original_link): bool
    {
        return (!strpos($original_link, '.'))? false : true;
    }

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    protected function generateRandomSlug($length = 5): string
    {
        if($length <= 0)
            throw new \Exception('Cannot create string when length is lower of equals 0');

        $chars = "qQwWeErRtTyYuUiIoOpPaAsSdDfFgGhHjJkKlLzZxXcCvVbBnNmM1234567890";
        $charsLength = strlen($chars)-1;
        $random = null;
        for($i = 0; $i<=$length; $i++)
        {
            $index = rand(0, $charsLength);
            $random = $random.$chars[$index];
        }

        return $random;
    }

    /**
     * @param $short
     * @return bool
     * @throws \Exception
     */
    protected function checkIsSlugExist($slug): bool
    {
        if($slug == NULL)
            throw new \Exception('Empty value of string');

        $repo = $this->entityManager->getRepository(Links::class);

        return ($repo->findOneBy(['slug' => $slug]) != NULL)? true : false;

    }
}