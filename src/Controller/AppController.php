<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Tom32i\ShowCaseBundle\Service\Browser;

class AppController extends AbstractController
{
    public function __construct(Browser $browser)
    {
        $this->$browser = $browser;
    }

    /**
     * @Route("/app", name="app")
     */
    public function index()
    {
        return $this->render('app/index.html.twig', [
            'gams' => $this->browser->list(),
        ]);
    }
}
