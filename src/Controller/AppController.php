<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Tom32i\ShowcaseBundle\Service\Browser;

class AppController extends AbstractController
{
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @Route("/", name="games")
     */
    public function games()
    {
        return $this->render('app/index.html.twig', [
            'games' => $this->browser->list(['[date]' => false], ['[slug]' => true]),
        ]);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function about()
    {
        return $this->render('app/about.html.twig');
    }

    /**
     * @Route("/{game}", name="game")
     */
    public function game(string $game)
    {
        $game = $this->browser->read($game, ['[slug]' => true]);

        if (!$game) {
            throw $this->createNotFoundException('Event not found');
        }

        $games = $this->browser->list(['[date]' => false], ['[slug]' => true]);
        $index = array_search($game, $games);
        $next = isset($games[$index + 1]) ? $games[$index + 1] : $games[count($games) - 1];
        $previous = isset($games[$index - 1]) ? $games[$index - 1] : $games[0];

        return $this->render('app/game.html.twig', [
            'game' => $game,
            'previous' => $previous,
            'next' => $next,
        ]);
    }
}
