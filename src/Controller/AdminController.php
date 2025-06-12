<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\GameType;
use App\Model\Game;
use App\Model\Screenshot;
use App\Service\GameRepository;
use App\Service\ScreenshotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tom32i\ShowcaseBundle\Service\Browser;
use Tom32i\ShowcaseBundle\Service\Normalizer;
use Tom32i\ShowcaseBundle\ValueResolver\Attribute\LoadOption;

#[Route(
    '/admin',
    name: 'admin_',
    condition: "'%kernel.environment%' !== 'prod'",
    options: ['stenope' => ['ignore' => true]]
)]
class AdminController extends AbstractController
{
    /**
     * @param Browser<Game, Screenshot> $browser
     */
    public function __construct(
        private Browser $browser,
        private Normalizer $normalizer,
        private GameRepository $gameRepository,
        private ScreenshotRepository $screenshotRepository,
    ) {
    }

    #[Route('/', name: 'list')]
    public function list(): Response
    {
        return $this->render('admin/list.html.twig', [
            'games' => $this->browser->list(
                ['[date]' => false],
                ['[slug]' => true],
                loadProps: LoadOption::onlyFirst(...)
            ),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(GameType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game = $this->gameRepository->create($form->getData());

            return $this->redirectToRoute('admin_show', ['game' => $game->getSlug()]);
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{game}', name: 'show')]
    public function show(Game $game): Response
    {
        return $this->render('admin/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{game}/edit', name: 'edit')]
    public function edit(Request $request, #[LoadOption(LoadOption::DISABLED)] Game $game): Response
    {
        $slug = $game->getSlug();
        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->gameRepository->update($game, $slug);

            return $this->redirectToRoute('admin_show', ['game' => $game->getSlug()]);
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form,
            'game' => $game,
        ]);
    }

    #[Route('/{game}/normalize', name: 'normalize')]
    public function normalize(#[LoadOption(LoadOption::DISABLED)] Game $game): Response
    {
        $this->normalizer->normalize($game);

        return $this->redirectToRoute('admin_show', ['game' => $game->getSlug()]);
    }

    #[Route('/{game}/sort/date', name: 'sort_date')]
    public function sortByDate(Game $game): Response
    {
        $game->sortByDate();
        $this->normalizer->normalize($game);

        return $this->redirectToRoute('admin_show', ['game' => $game->getSlug()]);
    }

    #[Route('/{game}/sort/shuffle', name: 'shuffle')]
    public function shuffle(#[LoadOption(LoadOption::DISABLED)] Game $game): Response
    {
        $game->shuffle();
        $this->normalizer->normalize($game);

        return $this->redirectToRoute('admin_show', ['game' => $game->getSlug()]);
    }

    #[Route('/{screenshot}/move/{direction}/{step}', name: 'move', requirements: [
        'screenshot' => '.+\/.+',
        'direction' => 'up|down|top|bottom',
        'step' => '\d*',
    ])]
    public function move(#[LoadOption(LoadOption::DISABLED)] Screenshot $screenshot, string $direction, int $step = 1): Response
    {
        if ($screenshot->move($direction, $step)) {
            $this->normalizer->normalize($screenshot->getGame());
        }

        return $this->redirectToRoute('admin_show', [
            'game' => $screenshot->getGame()->getSlug(),
            '_fragment' => $screenshot->getSlug(),
        ]);
    }

    #[Route('/{screenshot}/spoil/{spoil}', name: 'spoil', requirements: ['screenshot' => '.+\/.+', 'spoil' => '0|1'])]
    public function spoil(Screenshot $screenshot, int $spoil): Response
    {
        $screenshot->setSpoil((bool) $spoil);

        $this->screenshotRepository->save($screenshot);

        return $this->redirectToRoute('admin_show', [
            'game' => $screenshot->getGame()->getSlug(),
            '_fragment' => $screenshot->getSlug(),
        ]);
    }

    #[Route('/{screenshot}/delete', name: 'delete', requirements: ['screenshot' => '.+\/.+'])]
    public function delete(#[LoadOption(LoadOption::DISABLED)] Screenshot $screenshot): Response
    {
        $game = $screenshot->getGame();

        if ($this->screenshotRepository->delete($screenshot)) {
            $this->normalizer->normalize($game);
        }

        return $this->redirectToRoute('admin_show', [
            'game' => $game->getSlug(),
            '_fragment' => $screenshot->getSlug(),
        ]);
    }
}
