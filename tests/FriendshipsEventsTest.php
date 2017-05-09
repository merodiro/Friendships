<?php
namespace Tests;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Mockery;
class FriendshipsEventsTest extends TestCase
{
    use DatabaseMigrations;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->sender    = createUser();
        $this->recipient = createUser();
    }
  
    public function tearDown()
    {
        Mockery::close();
    }
  
    /** @test */
    public function friend_request_is_sent()
    {
        Event::shouldReceive('fire')->once()->withArgs(['friendrequest.sent', Mockery::any()]);
        
        $this->sender->addfriend($this->recipient);
    }
  
    /** @test */
    public function friend_request_is_accepted()
    {
        $this->sender->addfriend($this->recipient);
        Event::shouldReceive('fire')->once()->withArgs(['friendrequest.accepted', Mockery::any()]);
        
        $this->recipient->acceptFriend($this->sender);
    }  
  
    /** @test */
    public function friendship_is_cancelled()
    {
        $this->sender->addfriend($this->recipient);
        $this->recipient->acceptFriend($this->sender);
        Event::shouldReceive('fire')->once()->withArgs(['friendship.deleted', Mockery::any()]);
        
        $this->recipient->deleteFriend($this->sender);
    }
}