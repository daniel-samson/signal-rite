<?php

declare(strict_types=1);

namespace AppBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/insights")
 */
class InsightsController extends AbstractController
{

    /**
     * @Route("/", name="insights.index")
     */
    public function index(): Response
    {
        return $this->render('default/insights/index.html.twig');
    }
}
