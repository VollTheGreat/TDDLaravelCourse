<?php

use App\Concert;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    /** @test* */
    function calculating_the_total_cost()
    {
        // Arrange
        $tickets = collect([
            (object)['price' => 1200],
            (object)['price' => 1200],
            (object)['price' => 1200],
        ]);
        //Act
        $reservation = new Reservation($tickets);
        //Assert
        $this->assertEquals(3600, $reservation->totalCost());

    }

    /** @test* */
    function reserved_tickets_are_released_when_reservation_is_cancelled()
    {
        // Arrange
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);
        $reservation = new Reservation($tickets);
        //Act
        $reservation->cancel();
        //Assert
        foreach ($tickets as $ticket){
            $ticket->shouldHaveReceived('release');
        }
    }
}