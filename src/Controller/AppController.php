<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Annotation\Route;
use Tom32i\ShowcaseBundle\Service\Browser;

class AppController extends AbstractController
{
    private Browser $browser;
    private PropertyAccessor $propertyAccessor;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    private function listGames(): array
    {
        return $this->browser->list(['[date]' => false], ['[slug]' => true], ['[draft]' => false]);
    }

    /**
     * @Route("/", name="games")
     */
    public function games()
    {
        return $this->render('app/index.html.twig', [
            'games' => $this->listGames(),
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

        if (!$game || $this->propertyAccessor->getValue($game, '[draft]')) {
            throw $this->createNotFoundException('Game not found');
        }

        $games = $this->listGames();
        $index = array_search($game, $games);
        $next = isset($games[$index + 1]) ? $games[$index + 1] : $games[0];
        $previous = isset($games[$index - 1]) ? $games[$index - 1] : $games[\count($games) - 1];

        return $this->render('app/game.html.twig', [
            'game' => $game,
            'previous' => $previous,
            'next' => $next,
        ]);
    }
}
