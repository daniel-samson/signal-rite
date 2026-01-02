<?php

declare(strict_types=1);

namespace AppBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/charges")
 */
class ChargesController extends AbstractController
{

    /**
     * @Route("/", name = "charges.index")
     */
    public function index(): Response
    {
        return $this->render('default/charges/index.html.twig');
    }
}
