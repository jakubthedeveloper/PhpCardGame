<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\Game
 * @covers \PhpCardGame\Game\Player
 * @covers \PhpCardGame\Game\Player\Table
 * @covers \PhpCardGame\Game\Player\Cards
 * @covers \PhpCardGame\Game\Card
 * @covers \PhpCardGame\Game\Card\Power
 */
class GameTest extends TestCase
{
    public function testGameCanBeStarted(): void
    {
        $firstPlayer = new Player(
            name: "First player",
            cards: new Game\Player\Cards([
                new Card(
                    color: Card\Color::Blue,
                    points: 4,
                ),
                new Card(
                    color: Card\Color::Red,
                    points: 7,
                )
            ]),
            table: new Game\Player\Table()
        );

        $secondPlayer = new Player(
            name: "Second player",
            cards: new Game\Player\Cards([
                new Card(
                    color: Card\Color::Yellow,
                    points: 5,
                ),
                new Card(
                    color: Card\Color::Green,
                    points: 3,
                )
            ]),
            table: new Game\Player\Table()
        );

        $game = new Game(
            $firstPlayer,
            $secondPlayer
        );

        $game->start();

        $this->assertTrue($game->isStarted());
    }

    public function testGameCanNotBeStartedIfPlayersHaveTheSameName(): void
    {
        $firstPlayer = new Player(
            name: "John Doe",
            cards: new Game\Player\Cards([]),
            table: new Game\Player\Table()
        );

        $secondPlayer = new Player(
            name: "John Doe",
            cards: new Game\Player\Cards([]),
            table: new Game\Player\Table()
        );

        $game = new Game(
            $firstPlayer,
            $secondPlayer
        );

        $this->expectException(Game\Exception\InvalidPlayerNames::class);
        $this->expectExceptionMessage("Player names must be different, given: John Doe and John Doe.");

        $game->start();
    }

    public function testUserPutsCardOnTheTableAndAnotherUserCardsAreHit(): void
    {
        $firstPlayer = new Player(
            name: 'COMPUTER',
            cards: new Game\Player\Cards([
                new Card(
                    color: Card\Color::Blue,
                    points: 4,
                )
            ]),
            table: new Game\Player\Table([
                new Card(
                    color: Card\Color::Yellow,
                    points: 8,
                ),
                new Card(
                    color: Card\Color::Green,
                    points: 9,
                ),
                new Card(
                    color: Card\Color::Yellow,
                    points: 3,
                ),
                new Card(
                    color: Card\Color::Red,
                    points: 7,
                )
            ])
        );

        $secondPlayer = new Player(
            name: 'HUMAN',
            cards: new Game\Player\Cards([
                new Card(
                    color: Card\Color::Yellow,
                    points: 5,
                ),
                new Card(
                    color: Card\Color::Green,
                    points: 3,
                    powers: [
                        new Card\Power(
                            colorToHit: Card\Color::Yellow,
                            hitStrength: 5
                        ),
                        new Card\Power(
                            colorToHit: Card\Color::Red,
                            hitStrength: 2
                        )
                    ]
                )
            ]),
            table: new Game\Player\Table()
        );

        $game = new Game(
            $firstPlayer,
            $secondPlayer
        );

        $game->start();

        // Check users points before the hit
        $this->assertEquals(27, $firstPlayer->points());
        $this->assertEquals(0, $secondPlayer->points());

        // Check cards points before the hit
        $firstPlayerCards = $firstPlayer->getCardsOnTable();
        $this->assertEquals(8, $firstPlayerCards[0]->points());
        $this->assertEquals(9, $firstPlayerCards[1]->points());
        $this->assertEquals(3, $firstPlayerCards[2]->points());
        $this->assertEquals(7, $firstPlayerCards[3]->points());

        // Hits yellow cards by five points and red cards by two points
        $game->putSecondPlayerCard(1);

        // Check cards points after the hit
        $this->assertEquals(3, $firstPlayerCards[0]->points()); // 8 - 5
        $this->assertEquals(9, $firstPlayerCards[1]->points());
        $this->assertEquals(0, $firstPlayerCards[2]->points()); // 3 - 5 = -2, limit to 0
        $this->assertEquals(5, $firstPlayerCards[3]->points()); // 7 - 2

        // Check second player cards on the table
        $secondPlayerCardsOnTable = $secondPlayer->getCardsOnTable();
        $this->assertCount(1, $secondPlayerCardsOnTable);

        // Check second player cards on hand
        $secondPlayerCardsOnHand = $secondPlayer->getCardsOnHand();
        $this->assertCount(1, $secondPlayerCardsOnHand);

        // Check users points after the hit
        $this->assertEquals(17, $firstPlayer->points());
        $this->assertEquals(3, $secondPlayer->points());
    }

    public function testOnePlayerPutsCardOnTableAndOtherPlayerChangeItsValueThenUsersPointsChange(): void
    {
        $playerOneCard = new Card(
            color: Card\Color::Blue,
            points: 12,
        );

        $playerTwoCard = new Card(
            color: Card\Color::Yellow,
            points: 5,
            powers: [
                new Card\Power(
                    colorToHit: Card\Color::Blue,
                    hitStrength: 1
                ),
            ]
        );

        $playerOne = new Player(
            name: 'COMPUTER',
            cards: new Game\Player\Cards([
                $playerOneCard
            ]),
            table: new Game\Player\Table()
        );

        $playerTwo = new Player(
            name: 'HUMAN',
            cards: new Game\Player\Cards([
                $playerTwoCard
            ]),
            table: new Game\Player\Table()
        );

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $game->start();

        $this->assertEquals(0, $playerOne->points());
        $this->assertEquals(0, $playerTwo->points());

        $game->putFirstPlayerCard(0);

        $this->assertEquals(12, $playerOne->points());
        $this->assertEquals(0, $playerTwo->points());

        $game->putSecondPlayerCard(0);

        $this->assertEquals(11, $playerOne->points());
        $this->assertEquals(5, $playerTwo->points());
    }

    public function testCanNotPutCardIfGameIsNotStarted(): void
    {
        $playerOneCard = new Card(
            color: Card\Color::Blue,
            points: 12,
        );

        $playerOne = new Player(
            name: 'COMPUTER',
            cards: new Game\Player\Cards([
                $playerOneCard
            ]),
            table: new Game\Player\Table()
        );

        $playerTwo = new Player(
            name: 'HUMAN',
            cards: new Game\Player\Cards([]),
            table: new Game\Player\Table()
        );

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $this->expectException(Game\Exception\GameNotStarted::class);
        $this->expectExceptionMessage("Can't play because the game is not started.");

        $game->putFirstPlayerCard(0);
    }

    public function testCanNotPutCardIfGameIsFinished(): void
    {
        $playerOneCard = new Card(
            color: Card\Color::Blue,
            points: 12,
        );

        $playerOne = new Player(
            name: 'COMPUTER',
            cards: new Game\Player\Cards([
                $playerOneCard
            ]),
            table: new Game\Player\Table()
        );

        $playerTwo = new Player(
            name: 'HUMAN',
            cards: new Game\Player\Cards([]),
            table: new Game\Player\Table()
        );

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $game->start();
        $game->finish();

        $this->expectException(Game\Exception\GameFinished::class);
        $this->expectExceptionMessage("Can't play because the game is finished.");

        $game->putFirstPlayerCard(0);
    }

    public function testFirstPlayerWins(): void
    {
        $playerOne = $this->createMock(Player::class);
        $playerOne->expects($this->any())->method('name')->willReturn('Computer');

        $playerTwo = $this->createMock(Player::class);
        $playerTwo->expects($this->any())->method('name')->willReturn('Player');

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $game->start();
        $game->finish();

        $playerOne->expects($this->atLeastOnce())
             ->method('points')
             ->willReturn(25);

        $playerTwo->expects($this->atLeastOnce())
            ->method('points')
            ->willReturn(19);

        $this->assertEquals($playerOne, $game->getWinner());
    }

    public function testSecondPlayerWins(): void
    {
        $playerOne = $this->createMock(Player::class);
        $playerOne->expects($this->any())->method('name')->willReturn('Computer');

        $playerTwo = $this->createMock(Player::class);
        $playerTwo->expects($this->any())->method('name')->willReturn('Player');

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $game->start();
        $game->finish();

        $playerOne->expects($this->atLeastOnce())
            ->method('points')
            ->willReturn(20);

        $playerTwo->expects($this->atLeastOnce())
            ->method('points')
            ->willReturn(35);

        $this->assertEquals($playerTwo, $game->getWinner());
    }

    public function testNoWinnerOnDraw(): void
    {
        $playerOne = $this->createMock(Player::class);
        $playerOne->expects($this->any())->method('name')->willReturn('Computer');

        $playerTwo = $this->createMock(Player::class);
        $playerTwo->expects($this->any())->method('name')->willReturn('Player');

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $game->start();
        $game->finish();

        $playerOne->expects($this->atLeastOnce())
            ->method('points')
            ->willReturn(21);

        $playerTwo->expects($this->atLeastOnce())
            ->method('points')
            ->willReturn(21);

        $this->assertNull($game->getWinner());
    }

    public function testCanotGetWinnerIfGameHasNotBeenStarted(): void
    {
        $playerOne = $this->createMock(Player::class);
        $playerOne->expects($this->any())->method('name')->willReturn('Computer');

        $playerTwo = $this->createMock(Player::class);
        $playerTwo->expects($this->any())->method('name')->willReturn('Player');

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $this->expectException(Game\Exception\GameNotStarted::class);
        $this->expectExceptionMessage("Can't get the winner because the game has not been started.");
        $this->assertNull($game->getWinner());
    }

    public function testCanotGetWinnerIfGameIsNotFinished(): void
    {
        $playerOne = $this->createMock(Player::class);
        $playerOne->expects($this->any())->method('name')->willReturn('Computer');

        $playerTwo = $this->createMock(Player::class);
        $playerTwo->expects($this->any())->method('name')->willReturn('Player');

        $game = new Game(
            $playerOne,
            $playerTwo
        );

        $game->start();

        $this->expectException(Game\Exception\GameNotFinished::class);
        $this->expectExceptionMessage("Can't get the winner because the game is not finished yet.");
        $this->assertNull($game->getWinner());
    }
}
