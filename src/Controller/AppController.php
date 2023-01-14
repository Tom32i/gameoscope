<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tom32i\ShowcaseBundle\Model\Group;
use Tom32i\ShowcaseBundle\Service\Browser;

class AppController extends AbstractController
{
    public function __construct(
        private Browser $browser
    ) {
    }

    #[Route('/', name: 'games')]
    public function games(): Response
    {
        return $this->render('app/index.html.twig', [
            'games' => $this->listGames(),
        ]);
    }

    #[Route('/{game}', name: 'game')]
    public function game(string $game): Response
    {
        $game = $this->browser->read($game, ['[slug]' => true]);

        if ($game === null) {
            throw $this->createNotFoundException('Game not found');
        }

        if (($game['draft'] ?? false) === true) {
            throw $this->createNotFoundException('Game not found');
        }

        $games = $this->listGames();
        $index = (int) array_search($game, $games, true);
        $next = isset($games[$index + 1]) ? $games[$index + 1] : $games[0];
        $previous = isset($games[$index - 1]) ? $games[$index - 1] : $games[\count($games) - 1];

        return $this->render('app/game.html.twig', [
            'game' => $game,
            'previous' => $previous,
            'next' => $next,
        ]);
    }

    #[Route('/a-propos', name: 'about')]
    public function about(): Response
    {
        return $this->render('app/about.html.twig');
    }

    /**
     * @return Group[]
     */
    private function listGames(): array
    {
        return $this->browser->list(
            ['[date]' => false],
            ['[slug]' => true],
            ['[draft]' => false]
        );
    }
}
