<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Game;
use App\Model\Screenshot;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tom32i\ShowcaseBundle\Service\Browser;
use Tom32i\ShowcaseBundle\ValueResolver\Attribute\LoadOption;

class AppController extends AbstractController
{
    /**
     * @param Browser<Game, Screenshot> $browser
     */
    public function __construct(
        private Browser $browser,
    ) {
    }

    #[Route('/', name: 'games')]
    public function games(): Response
    {
        return $this->render('app/index.html.twig', [
            'games' => $this->listGames(LoadOption::onlyFirst(...)),
        ]);
    }

    #[Route('/a-propos', name: 'about')]
    public function about(): Response
    {
        return $this->render('app/about.html.twig');
    }

    #[Route('/{game}', name: 'game')]
    public function game(Game $game): Response
    {
        if ($this->isProd() && $game->isDraft()) {
            throw $this->createNotFoundException('Game not found');
        }

        $games = $this->listGames(LoadOption::disabled(...));
        $index = (int) array_search($game, $games, true);
        $next = isset($games[$index + 1]) ? $games[$index + 1] : $games[0];
        $previous = isset($games[$index - 1]) ? $games[$index - 1] : $games[\count($games) - 1];

        return $this->render('app/game.html.twig', [
            'game' => $game,
            'previous' => $previous,
            'next' => $next,
        ]);
    }

    /**
     * @return Game[]
     */
    private function listGames(?callable $loadProps = null): array
    {
        return $this->browser->list(
            ['[date]' => false],
            ['[slug]' => true],
            $this->isProd() ? ['[draft]' => false] : null,
            loadProps: $loadProps
        );
    }

    private function isProd(): bool
    {
        return $this->getParameter('kernel.environment') === 'prod';
    }
}
