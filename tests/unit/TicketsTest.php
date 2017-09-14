<?php

use App\Concert;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test* */
    function ticket_can_be_released()
    {
        // Arrange
        $ticket = factory(Ticket::class)->states('reserved')->create();
        $this->assertNotNull($ticket->reserved_at);
        //Act
        $ticket->release();
        //Assert
        $this->assertNull($ticket->fresh()->reserved_at);

    }

    /** @test* */
    function ticket_can_be_reserved()
    {
        // Arrange
        $ticket = factory(Ticket::class)->create();
        //Act
        $ticket->reserve();
        //Assert
        $this->assertNotNull($ticket->fresh()->reserved_at);

    }
}