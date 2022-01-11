<?php

namespace PhpCardGame;

use PhpCardGame\Game\Cards;
use PhpCardGame\Game\Exception\GameFinished;
use PhpCardGame\Game\Exception\GameNotFinished;
use PhpCardGame\Game\Exception\GameNotStarted;
use PhpCardGame\Game\Exception\InvalidPlayerNames;
use PhpCardGame\Game\Player;

class Game
{
    private bool $isStarted = false;
    private bool $isFinished = false;

    public function __construct(
        public readonly Player $firstPlayer,
        public readonly Player $secondPlayer
    ) {

    }

    /**
     * @throws InvalidPlayerNames
     */
    public function start(): void
    {
        $this->validateGame();

        $this->isFinished = false;
        $this->isStarted = true;
    }

    public function finish(): void
    {
        $this->isFinished = true;
    }

    private function validateGame(): void
    {
        if ($this->firstPlayer->name() === $this->secondPlayer->name()) {
            throw new InvalidPlayerNames(
                sprintf(
                    "Player names must be different, given: %s and %s.",
                    $this->firstPlayer->name(),
                    $this->secondPlayer->name()
                )
            );
        }
    }

    /**
     * @throws Game\Exception\CardDoesNotExist
     */
    public function putFirstPlayerCard(int $cardIndex): void
    {
        $this->validateGameGoesOn();

        $card = $this->firstPlayer->popCardFromHand($cardIndex);

        $this->firstPlayer->putCardOnTable($card);

        $this->secondPlayer->hitTableBy($card);
    }

    /**
     * @throws Game\Exception\CardDoesNotExist
     */
    public function putSecondPlayerCard(int $cardIndex): void
    {
        $this->validateGameGoesOn();

        $card = $this->secondPlayer->popCardFromHand($cardIndex);

        $this->secondPlayer->putCardOnTable($card);

        $this->firstPlayer->hitTableBy($card);
    }

    public function isStarted(): bool
    {
        return $this->isStarted;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function getWinner(): ?Player
    {
        if (false === $this->isStarted()) {
            throw new GameNotStarted("Can't get the winner because the game has not been started.");
        }

        if (false === $this->isFinished()) {
            throw new GameNotFinished("Can't get the winner because the game is not finished yet.");
        }

        if ($this->firstPlayer->points() > $this->secondPlayer->points()) {
            return $this->firstPlayer;
        }

        if ($this->firstPlayer->points() < $this->secondPlayer->points()) {
            return $this->secondPlayer;
        }

        return null;
    }

    /**
     * @throws GameFinished
     * @throws GameNotStarted
     */
    private function validateGameGoesOn(): void
    {
        if (false === $this->isStarted()) {
            throw new GameNotStarted("Can't play because the game is not started.");
        }

        if (true === $this->isFinished()) {
            throw new GameFinished("Can't play because the game is finished.");
        }
    }
}
