<?php

namespace App\Controller;

use App\Entity\Links;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as API;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function addNewShort(Request $request)
    {
        $originalLink = $request->query->get('original_link');

        if(empty($originalLink))
            return $this->createErrorResponse('Link cannot be empty!', 400);

        try {
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
    }

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    protected function generateRandomString($length = 5): string
    {
        if($length <= 0)
            throw new \Exception('Cannot create string where length is lower of equals 0');

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
    protected function checkIsShortLinkExist($short): Bool
    {
        if($short == NULL)
            throw new \Exception('Empty value of string');

        $repo = $this->getDoctrine()->getRepository(Links::class);

        return ($repo->findOneBy(['short_link' => $short]) != NULL)? true : false;

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