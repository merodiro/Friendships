<?php

use Illuminate\Support\Facades\Event;

class FriendshipsEventsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->sender    = factory(User::class)->create();
        $this->recipient = factory(User::class)->create();
    }
  
    /** @test */
    public function friend_request_is_sent()
    {
        Event::shouldReceive('fire')
            ->once()
            ->withArgs(['friendrequest.sent', Mockery::any()]);
        
        $this->sender->addfriend($this->recipient);
    }
  
    /** @test */
    public function friend_request_is_accepted()
    {
        $this->sender->addfriend($this->recipient);

        Event::shouldReceive('fire')
            ->once()
            ->withArgs(['friendrequest.accepted', Mockery::any()]);
        
        $this->recipient->acceptFriend($this->sender);
    }
  
    /** @test */
    public function friendship_is_cancelled()
    {
        $this->sender->addfriend($this->recipient);
        $this->recipient->acceptFriend($this->sender);

        Event::shouldReceive('fire')
            ->once()
            ->withArgs(['friendship.deleted', Mockery::any()]);
        
        $this->recipient->deleteFriend($this->sender);
    }
}
