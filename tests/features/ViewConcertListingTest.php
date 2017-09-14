<?php
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;
    /** @test**/
    function user_can_view_a_published_concert_listing(){
            $concert = factory(Concert::class)->states('published')->create([
               'title' => 'Rocks Kiss',
               'subtitle'=> 'best band ever',
               'date' => Carbon::parse('December 13, 2016 7:00pm'),
               'published_at' => Carbon::parse('-1 week'),
               'ticket_price'=> 2230,
               'venue'=> 'MoshPit',
               'venue_address'=> '123 example lane',
               'city'=> 'laraville',
               'state'=> 'on',
               'zip'=> '51-165',
               'additional_information' => 'for tickets call (55555)'
            ]);


            $this->visit('/concerts/'.$concert->id);

            $this->see('Rocks Kiss');
            $this->see('best band ever');
            $this->see('December 13, 2016');
            $this->see('7:00pm');
            $this->see('51-165');
    }
    /** @test* */
    function user_cannot_view_unpublished_concerts()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $this->get('/concerts/'.$concert->id);

        $this->assertResponseStatus(404);
    }
}
