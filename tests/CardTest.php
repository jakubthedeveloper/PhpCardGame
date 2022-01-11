<?php

namespace Game;

use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCardHasRequiredAttributes()
    {
        $card = new Card(
            name: "My first card",
            color: Card\Color::Blue,
            points: 7,
        );

        $this->assertEquals("My first card", $card->name);
        $this->assertEquals(Card\Color::Blue, $card->color);
        $this->assertEquals(7, $card->points());
    }

    public function testCardHasPowers()
    {
        $card = new Card(
            name: "My card with powers",
            color: Card\Color::Blue,
            points: 10,
            powers: [
                new Card\Power\HitsColor(
                    colorToHit: Card\Color::Red,
                    hitStrength: 5
                ),
                new Card\Power\HitsColor(
                    colorToHit: Card\Color::Green,
                    hitStrength: 2
                )
            ]
        );

        $this->assertEquals(
            Card\Color::Red,
            $card->powers[0]->colorToHit
        );

        $this->assertEquals(
            5,
            $card->powers[0]->hitStrength
        );

        $this->assertEquals(
            Card\Color::Green,
            $card->powers[1]->colorToHit
        );

        $this->assertEquals(
            2,
            $card->powers[1]->hitStrength
        );
    }

    public function testCardHitsAnotherCard()
    {
        $power = new Card\Power\HitsColor(
            colorToHit: Card\Color::Yellow,
            hitStrength: 3
        );

        $cardThatHits = new Card(
            name: "My Red Card",
            color: Card\Color::Red,
            points: 5,
            powers: [
                $power
            ]
        );

        $cartThatIsBeingHit = new Card(
            name: "Victim Card",
            color: Card\Color::Yellow,
            points: 10
        );

        $this->assertEquals(10, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(7, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(4, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(1, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(0, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(0, $cartThatIsBeingHit->points());
    }

    public function testCardDoesNotHitIfPowersDoesNotAllow()
    {
        $power = new Card\Power\HitsColor(
            colorToHit: Card\Color::Blue,
            hitStrength: 3
        );

        $cardThatHits = new Card(
            name: "My Red Card",
            color: Card\Color::Red,
            points: 5,
            powers: [
                $power
            ]
        );

        $cartThatIsBeingHit = new Card(
            name: "Victim Card",
            color: Card\Color::Yellow,
            points: 10
        );

        $this->assertEquals(10, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(10, $cartThatIsBeingHit->points());
    }
}
