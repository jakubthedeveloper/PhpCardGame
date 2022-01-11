<?php

namespace PhpCardGame\UI\Command;

use PhpCardGame\Game;
use PhpCardGame\Game\GameFactory;
use PhpCardGame\UI\Exception\InvalidTerminalSize;
use PhpCardGame\UI\Render\CardRenderer;
use PhpCardGame\UI\Render\ColorMapper;
use PhpCardGame\UI\Render\HorizontalLinesMerger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;

enum Action: string
{
    case START = 'start';
    case QUIT = 'quit';
}

class PlayGameCommand extends Command
{
    const CLEAR_SCREEN_STRING = "\033\143";

    const POINTS = 36;
    const CARDS_AMOUNT = 6;
    const POWER_POINTS_ON_CARD = 10;
    const COMPUTER_THINKING_TIME = 2; // seconds
    const MIN_TERMINAL_WIDTH = 120;
    const MIN_TERMINAL_HEIGHT = 30;

    protected static $defaultName = 'game:play';

    private CardRenderer $cardRenderer;
    private ColorMapper $colorMapper;
    private HorizontalLinesMerger $horizontalLinesMerger;

    protected function configure(): void
    {
        $this->cardRenderer = new CardRenderer();
        $this->colorMapper = new ColorMapper();
        $this->horizontalLinesMerger = new HorizontalLinesMerger();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->clearScreen($io);

        try {
            $this->validateTerminalSize();
        } catch (InvalidTerminalSize $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $io->title('PHP 8 Card Game');
        $io->writeln("https://github.com/jakubthedeveloper/PhpCardGame");

        while (true) {
            $action = $this->selectAction($io);

            if ($action === Action::QUIT->value) {
                $io->writeln("Bye bye.");
                return Command::SUCCESS;
            }

            $this->clearScreen($io);

            $game = GameFactory::createGame(
                firstPlayerName: "Computer",
                secondPlayerName: "Player",
                points: self::POINTS,
                cardsAmount: self::CARDS_AMOUNT,
                powerPointsOnCard: self::POWER_POINTS_ON_CARD
            );

            $this->gameLoop($io, $game);
        }
    }

    private function validateTerminalSize(): void
    {
        $terminal = new Terminal();
        $width = $terminal->getWidth();
        $height = $terminal->getHeight();

        if ($width < self::MIN_TERMINAL_WIDTH) {
            throw new InvalidTerminalSize(
                sprintf("The terminal must be at least %d columns wide.", self::MIN_TERMINAL_WIDTH)
            );
        }

        if ($height < self::MIN_TERMINAL_HEIGHT) {
            throw new InvalidTerminalSize(
                sprintf("The terminal must be at least %d lines high.", self::MIN_TERMINAL_HEIGHT)
            );
        }
    }

    private function clearScreen(SymfonyStyle $io): void
    {
        $io->write(self::CLEAR_SCREEN_STRING);
    }

    private function gameLoop(SymfonyStyle $io, Game $game): void
    {
        $game->start();

        while (false === $game->isFinished()) {
            $this->clearScreen($io);
            $this->render($io, $game);
            $this->userTurn($io, $game);

            $this->clearScreen($io);
            $this->render($io, $game);
            $this->computerTurn($io, $game);

            $this->checkFinished($game);
        }

        $this->clearScreen($io);
        $this->render($io, $game);

        $io->info("Game over");
        $winner = $game->getWinner();

        if ($winner instanceof Game\Player) {
            $io->info(
                sprintf("%s Wins!", $winner->name())
            );
        } else {
            $io->info(
                sprintf("Draw! No winner.")
            );
        }
    }

    private function checkFinished(Game $game): void
    {
        if (empty($game->firstPlayer->getCardsOnHand())) {
            $game->finish();
            return;
        }

        if (empty($game->secondPlayer->getCardsOnHand())) {
            $game->finish();
        }
    }

    private function render(SymfonyStyle $io, Game $game): void
    {
        $io->table(
            ['Player', 'Points', 'Cards in hand'],
            [
                [
                    $game->firstPlayer->name(),
                    $game->firstPlayer->points(),
                    count($game->firstPlayer->getCardsOnHand())
                ],
                [
                    $game->secondPlayer->name(),
                    $game->secondPlayer->points(),
                    count($game->secondPlayer->getCardsOnHand())
                ]
            ]
        );

        $this->drawTable($io, $game);

        $io->comment("Cards in your hand:");

        $this->drawCardsHorizontally(
            io: $io,
            cards: $game->secondPlayer->getCardsOnHand()
        );
    }

    private function selectAction(SymfonyStyle $io): mixed
    {
        return $io->choice(
            "Select an action:",
            [
                Action::START->value => 'Start new game',
                Action::QUIT->value => 'Quit'
            ]
        );
    }

    /**
     * @param Game\Card[] $cards
     */
    private function drawCardsHorizontally(SymfonyStyle $io, array $cards): void
    {
        $cardsToMerge = [];

        $cardNumber = 1;
        foreach ($cards as $card) {
            $cardsToMerge[] = $this->cardRenderer->getLines($card, $cardNumber);
            $cardNumber++;
        }

        for ($i = $cardNumber; $i <= self::CARDS_AMOUNT; $i++) {
            $cardsToMerge[] = $this->cardRenderer->getCardSlotLines();
        }

        foreach ($this->horizontalLinesMerger->mergeLines($cardsToMerge) as $mergedLine) {
            $io->writeln($mergedLine);
        }
    }

    private function userTurn(SymfonyStyle $io, Game $game): void
    {
        $choices = [];
        $key = 1;
        foreach ($game->secondPlayer->getCardsOnHand() as $card) {
            $choices[$key] = sprintf(
                "<fg=%s>#%d %s (%d)</>",
                $this->colorMapper->getDrawColor($card->color),
                $key,
                $card->color->name,
                $card->points()
            );
            $key++;
        }

        $answer = $io->choice(
            'Please select card to put on the table',
            $choices
        );

        $cardToPut = array_search($answer, $choices) - 1;

        $game->putSecondPlayerCard($cardToPut);
    }

    private function computerTurn(SymfonyStyle $io, Game $game): void
    {
        if (count($game->firstPlayer->getCardsOnHand()) === 0) {
            return;
        }

        $io->info("Waiting for computer card...");
        sleep(self::COMPUTER_THINKING_TIME);

        $cardIndex = random_int(0, count($game->firstPlayer->getCardsOnHand()) - 1);

        $game->putFirstPlayerCard($cardIndex);
    }

    private function drawTable(SymfonyStyle $io, Game $game): void
    {
        $io->writeln("Cards on the table.");

        $io->writeln($game->firstPlayer->name());

        $this->drawCardsHorizontally(
            io: $io,
            cards: $game->firstPlayer->getCardsOnTable()
        );

        $io->writeln($game->secondPlayer->name());

        $this->drawCardsHorizontally(
            io: $io,
            cards: $game->secondPlayer->getCardsOnTable()
        );
    }
}
