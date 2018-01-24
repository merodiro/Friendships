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
        Event::shouldReceive('dispatch')
            ->once()
            ->withArgs(['friendrequest.sent', [$this->sender, $this->recipient]]);
        
        $this->sender->addfriend($this->recipient);
    }
  
    /** @test */
    public function friend_request_is_accepted()
    {
        $this->sender->addfriend($this->recipient);

        Event::shouldReceive('dispatch')
            ->once()
            ->withArgs(['friendrequest.accepted', [$this->recipient, $this->sender]]);
        
        $this->recipient->acceptFriend($this->sender);
    }
  
    /** @test */
    public function friendship_is_cancelled()
    {
        $this->sender->addfriend($this->recipient);
        $this->recipient->acceptFriend($this->sender);

        Event::shouldReceive('dispatch')
            ->once()
            ->withArgs(['friendship.deleted', [$this->recipient, $this->sender]]);
        
        $this->recipient->deleteFriend($this->sender);
    }
}
