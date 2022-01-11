<?php

namespace PhpCardGame\Game;

use JetBrains\PhpStorm\Pure;
use PhpCardGame\Game;
use PhpCardGame\Game\Player\Cards;
use PhpCardGame\Game\Player\Table;

class GameFactory
{
    public static function createGame(
        string $firstPlayerName,
        string $secondPlayerName,
        int $points,
        int $cardsAmount,
        int $powerPointsOnCard
    ): Game {
        self::validatePointsAndCards($points, $cardsAmount);
        self::validatePowerPointsOnCard($powerPointsOnCard);

        return new Game(
            firstPlayer: new Player(
                name: $firstPlayerName,
                cards: self::generateCards($points, $cardsAmount, $powerPointsOnCard),
                table: new Table()
            ),
            secondPlayer: new Player(
                name: $secondPlayerName,
                cards: self::generateCards($points, $cardsAmount, $powerPointsOnCard),
                table: new Table()
            )
        );
    }

    /**
     * @throws Exception\InvalidGameConfiguration
     */
    private static function validatePointsAndCards(int $points, int $cardsAmount): void
    {
        if ($points <= $cardsAmount) {
            throw new Game\Exception\InvalidGameConfiguration(
                sprintf(
                    "Points must be greater than number of cards. Given: points=%d, cards=%d.",
                    $points,
                    $cardsAmount
                )
            );
        }

        if ($points % $cardsAmount !== 0) {
            throw new Game\Exception\InvalidGameConfiguration(
                sprintf(
                    "Points must be divisible by the number of cards with no remainder. Given: points=%d, cards=%d.",
                    $points,
                    $cardsAmount
                )
            );
        }
    }

    /**
     * @throws Exception\InvalidGameConfiguration
     */
    private static function validatePowerPointsOnCard(int $powerPointsOnCard): void
    {
        if ($powerPointsOnCard <= 0) {
            throw new Game\Exception\InvalidGameConfiguration(
                sprintf(
                    "Points must be greater than zero. Given: %d.",
                    $powerPointsOnCard
                )
            );
        }
    }

    /**
     * return Card[]
     */
    private static function generateCards(int $pointsToDistribute, int $cardsAmount, int $powerPointsOnCard): Cards
    {
        $cards = new Cards();

        for ($i = 0; $i < $cardsAmount; $i++) {
            if ($i === $cardsAmount - 1) {
                $val = $pointsToDistribute;
            } else {
                $val = rand(1, (int)floor($pointsToDistribute/2));
            }

            $cardColor = Game\Card\Color::random();

            $cards->add(
                new Card(
                    color: $cardColor,
                    points: $val,
                    powers: self::generatePowers($cardColor, $powerPointsOnCard)
                )
            );
            $pointsToDistribute -= $val;
        }

        return $cards;
    }

    /**
     * @return Game\Card\Power[]
     */
    private static function generatePowers(Game\Card\Color $cardColor, int $powerPointsToDistribute): array
    {
        $colors = [];

        foreach (Game\Card\Color::cases() as $case) {
            if ($case !== $cardColor) {
                $colors[] = $case;
            }
        }

        $powers = [];

        foreach ($colors as $key => $color) {
            if ($key < count($colors) - 1) {
                $val = rand(1, (int)floor($powerPointsToDistribute/2));
            } else {
                // add remaining points to the last card
                $val = $powerPointsToDistribute;
            }

            $powers[] = new Game\Card\Power(
                colorToHit: $color,
                hitStrength: $val
            );
            $powerPointsToDistribute -= $val;
        }

        return $powers;
    }
}
