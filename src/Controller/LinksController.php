<?php

namespace App\Controller;

use App\Entity\Links;
use Fig\Link\Link;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as API;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\LinkBundle;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LinksController
 * @Route("/api", name="Links-api")
 */
class LinksController extends AbstractFOSRestController
{
    /**TODO
     * Add Delete and update methods
     * Add Authorization
     * Add unit test
     * Add exceptions for empty request
     */


    /**
     * @API\Get("/links")
     * @return Response
     */
    public function getListOfLinks(LinkBundle $linkBundle)
    {
        try {
            $data = $linkBundle->getListOfLinks();
        }
        catch(\Exception $e){
            return $this->createErrorResponse($e->getMessage());
        }
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * @API\Post("/links")
     * @return Response
     * @throws \Exception, AccessDeniedException
     */
    public function addNewShort(Request $request, LinkBundle $linkBundle)
    {
        try {
            $link = $request->request->get('original_link');
            $isLink = $linkBundle->checkIsLinkInDataBase($link);

            if($isLink)
                return $this->handleView($this->view($linkBundle->getExistedShortLink($link), Response::HTTP_ALREADY_REPORTED));

            $slug = $linkBundle->getUniqueSlug();
            $short_link = $linkBundle->createNewLink($slug);

            $data = $linkBundle->addLinkToDatabase($link, $short_link, $slug);
        }
        catch(AccessDeniedException $acces){
            return $this->createErrorResponse($acces->getMessage(), 403);
        }
        catch (\Exception $e){
            return $this->createErrorResponse($e->getMessage());
        }


        $view = $this->view($data, Response::HTTP_CREATED);
        return $this->handleView($view);
    }

    /**
     * @API\Get("/links/{slug}")
     * @return Response
     * @throws \Exception
     * @param $slug
     */
    public function getElementBySlug($slug, LinkBundle $linkBundle)
    {
        try{
            $data = $linkBundle->getElementBySlug($slug);
        }
        catch(\Exception $e){
            $this->createErrorResponse($e->getMessage());
        }

        $view = $this->view($data);
        return $this->handleView($view);
    }

    /**
     * @param $message
     * @param string $code
     * @return Response
     */
    protected function createErrorResponse($message='Something goes wrong', $Httpcode='500')
    {
        $view = $this->view([
            'status' => 'Error',
            'message' => $message,
        ], $Httpcode);

        return $this->handleView($view);
    }
}