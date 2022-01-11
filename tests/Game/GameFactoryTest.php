<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game;
use PhpCardGame\Game\Exception\InvalidGameConfiguration;
use PhpCardGame\Game\Exception\InvalidPlayerNames;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\Game\GameFactory
 * @covers \PhpCardGame\Game
 * @covers \PhpCardGame\Game\Card
 * @covers \PhpCardGame\Game\Card\Color
 * @covers \PhpCardGame\Game\Player
 * @covers \PhpCardGame\Game\Player\Cards
 * @covers \PhpCardGame\Game\Player\Table
 * @covers \PhpCardGame\Game\Card\Power
 */
class GameFactoryTest extends TestCase
{
    public function testNumberOfCardsMustBeGreaterThanPoints(): void
    {
        $this->expectException(InvalidGameConfiguration::class);
        $this->expectExceptionMessage("Points must be greater than number of cards. Given: points=4, cards=6.");

        GameFactory::createGame(
            firstPlayerName: "Computer",
            secondPlayerName: "Player",
            points: 4,
            cardsAmount: 6,
            powerPointsOnCard: 4
        );
    }

    public function testPointsMustBeDivisibleByNumberOfCards(): void
    {
        $this->expectException(InvalidGameConfiguration::class);
        $this->expectExceptionMessage("Points must be divisible by the number of cards with no remainder. Given: points=32, cards=6.");

        GameFactory::createGame(
            firstPlayerName: "Computer",
            secondPlayerName: "Player",
            points: 32,
            cardsAmount: 6,
            powerPointsOnCard: 4
        );
    }

    public function testPowerPointsOnCardMustBeGreaterThanZero(): void
    {
        $this->expectException(InvalidGameConfiguration::class);
        $this->expectExceptionMessage("Points must be greater than zero. Given: -5.");

        GameFactory::createGame(
            firstPlayerName: "Computer",
            secondPlayerName: "Player",
            points: 36,
            cardsAmount: 6,
            powerPointsOnCard: -5
        );
    }

    public function testPlayerNamesCannotBeTheSame(): void
    {
        $game = GameFactory::createGame(
            firstPlayerName: "Player",
            secondPlayerName: "Player",
            points: 36,
            cardsAmount: 6,
            powerPointsOnCard: 4
        );

        $this->expectException(InvalidPlayerNames::class);
        $this->expectExceptionMessage("Player names must be different, given: Player and Player.");

        $game->start();
    }

    public function testGameCanBeCreated(): void
    {
        $game = GameFactory::createGame(
            firstPlayerName: "Computer",
            secondPlayerName: "Player",
            points: 36,
            cardsAmount: 6,
            powerPointsOnCard: 4
        );

        $this->assertInstanceOf(Game::class, $game);
    }

    public function testPointsAreDistributedOnCards(): void
    {
        $points = 36;

        $game = GameFactory::createGame(
            firstPlayerName: "Computer",
            secondPlayerName: "Player",
            points: $points,
            cardsAmount: 6,
            powerPointsOnCard: 4
        );

        $this->assertCount(6, $game->firstPlayer->getCardsOnHand());
        $this->assertCount(6, $game->secondPlayer->getCardsOnHand());

        $firstPlayerAllPointsOnCards = 0;
        foreach ($game->firstPlayer->getCardsOnHand() as $card) {
            $this->assertGreaterThan(0, $card->points());
            $this->assertLessThanOrEqual($points / 2, $card->points());
            $firstPlayerAllPointsOnCards += $card->points();
        }
        $this->assertEquals($points, $firstPlayerAllPointsOnCards);

        $secondPlayerAllPointsOnCards = 0;
        foreach ($game->secondPlayer->getCardsOnHand() as $card) {
            $this->assertGreaterThan(0, $card->points());
            $this->assertLessThanOrEqual($points / 2, $card->points());
            $secondPlayerAllPointsOnCards += $card->points();
        }
        $this->assertEquals($points, $secondPlayerAllPointsOnCards);
    }


    public function testPowerPointsAreDistributedOnCards(): void
    {
        $powerPointsOnCard = 8;

        $game = GameFactory::createGame(
            firstPlayerName: "Computer",
            secondPlayerName: "Player",
            points: 36,
            cardsAmount: 6,
            powerPointsOnCard: $powerPointsOnCard
        );

        foreach ($game->firstPlayer->getCardsOnHand() as $card) {
            $cardPowerPointsSum = 0;

            /** @var Game\Card\Power $power */
            foreach ($card->powers as $power) {
                $cardPowerPointsSum += $power->hitStrength;
            }

            $this->assertEquals($powerPointsOnCard, $cardPowerPointsSum);
        }

        foreach ($game->secondPlayer->getCardsOnHand() as $card) {
            $cardPowerPointsSum = 0;

            /** @var Game\Card\Power $power */
            foreach ($card->powers as $power) {
                $cardPowerPointsSum += $power->hitStrength;
            }

            $this->assertEquals($powerPointsOnCard, $cardPowerPointsSum);
        }
    }
}
