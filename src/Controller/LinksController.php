<?php

namespace App\Controller;

use App\Entity\Links;
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
    /**
     * @API\Get("/links")
     * @return Response
     */
    public function getListOfLinks()
    {
        $manager = $this->getDoctrine()->getRepository(Links::class);

        $data = $manager->findAll();

        $view = $this->view($data, 200);

        return $this->handleView($view);
    }

    /**
     * @API\Post("/links")
     * @return Response
     * @throws \Exception
     */
    public function addNewShort(Request $request, LinkBundle $linkBundle)
    {
        try {
            $link = $request->query->get('original_link');

            $isLink = $linkBundle->checkIsLinkInDataBase($link);

            if($isLink)
                return $this->handleView($this->view($linkBundle->getExistedShortLink($link)));

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


        $view = $this->view($data);
        return $this->handleView($view);
    }

   /* public function addNewShort(Request $request)
    {
        $originalLink = $request->query->get('original_link');

        if(empty($originalLink))
            return $this->createErrorResponse('Link cannot be empty!', 400);

        try
        {
            for($i = 0; $i<10; $i++)
            {
                $short = $this->generateRandomString();
                if(!$this->checkIsShortLinkExist($short))
                    break;
            }

            if($i == 10)
                throw new \Exception('Infinite loop: cannot find free link');

            $link = new Links();
            $link->setOriginalLink($originalLink);
            $link->setShortLink('www.skroc.to/'.$short);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($link);
            $manager->flush();
        }
        catch(\Exception $e){
            return $this->createErrorResponse($e->getMessage());
        }

        $view = $this->view($link, 201);
        return $this->handleView($view);
    }*/

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