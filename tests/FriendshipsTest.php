<?php


class FriendshipTest extends TestCase
{

    /** @test */
    public function user_can_send_a_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);

        $this->assertEquals(1, $recipient->friendRequestsReceived()->count());
        $this->assertEquals(1, $sender->friendRequestsSent()->count());

        $this->assertNotTrue($sender->isFriendsWith($recipient));
        $this->assertNotTrue($recipient->isFriendsWith($sender));
    }

    /** @test */
    public function user_can_not_send_a_friend_request_if_frienship_is_pending()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);
        $sender->addFriend($recipient);
        $sender->addFriend($recipient);

        $this->assertEquals(1, $recipient->friendRequestsReceived()->count());
    }

    /** @test */
    public function user_can_send_a_friend_request_if_frienship_is_denied()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);

        $recipient->deleteFriend($sender);
        $this->assertEquals(0, $recipient->friendRequestsReceived()->count());


        $sender->addFriend($recipient);
        $this->assertEquals(1, $recipient->friendRequestsReceived()->count());
    }

    /** @test */
    public function user_can_remove_a_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);
        $sender->deleteFriend($recipient);
        $this->assertEquals(0, $recipient->friendRequestsReceived()->count());


        $sender->addFriend($recipient);
        $this->assertEquals(1, $recipient->friendRequestsReceived()->count());


        $recipient->acceptfriend($sender);
        $this->assertEquals('friends', $recipient->checkFriendship($sender));

        $sender->deleteFriend($recipient);
        $this->assertEquals('not_friends', $recipient->checkFriendship($sender));
    }

    /** @test */
    public function change_statue_to_pending_and_waiting()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);

        $this->assertEquals('pending', $recipient->checkFriendship($sender));
        $this->assertEquals('waiting', $sender->checkFriendship($recipient));
    }

    /** @test */
    public function user_can_not_send_a_friend_request_to_himself()
    {
        $user = factory(User::class)->create();

        $user->addFriend($user);

        $this->assertEquals('same_user', $user->checkFriendship($user));
        $this->assertEquals(0, $user->friendRequestsReceived()->count());

        $this->assertEquals(0, $user->friendRequestsSent()->count());
    }

    /** @test */
    public function user_is_friend_with_another_user_if_accepts_a_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);
        $recipient->acceptFriend($sender);

        $this->assertEquals('friends', $recipient->checkFriendship($sender));
        $this->assertEquals('friends', $sender->checkFriendship($recipient));

        $this->assertTrue($sender->isFriendsWith($recipient));
        $this->assertTrue($recipient->isFriendsWith($sender));
    }

    /** @test */
    public function user_has_no_friend_request_from_if_he_accepted_the_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);
        $recipient->acceptFriend($sender);

        $this->assertEquals(0, $recipient->friendRequestsReceived()->count());

        $this->assertEquals(0, $sender->friendRequestsSent()->count());
    }

    /** @test */
    public function user_cannot_accept_his_own_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();
        $sender->addFriend($recipient);
        $sender->acceptFriend($recipient);

        $this->assertEquals('pending', $recipient->checkFriendship($sender));
    }

    /** @test */
    public function it_returns_accepted_friendships()
    {
        $sender = factory(User::class)->create();
        $recipients = factory(User::class, 3)->create();

        foreach ($recipients as $recipient) {
            $sender->addFriend($recipient);
        }

        $recipients[0]->acceptFriend($sender);
        $recipients[1]->acceptFriend($sender);
        $recipients[2]->deleteFriend($sender);

        $this->assertEquals(2, $sender->friends()->count());
    }

    /** @test */
    public function it_returns_only_accepted_user_friendships()
    {
        $sender = factory(User::class)->create();
        $recipients = factory(User::class, 4)->create();

        foreach ($recipients as $recipient) {
            $sender->addFriend($recipient);
        }

        $recipients[0]->acceptFriend($sender);
        $recipients[1]->acceptFriend($sender);
        $recipients[2]->deleteFriend($sender);

        $this->assertEquals(2, $sender->friends()->count());

        $this->assertEquals(1, $recipients[0]->friends()->count());
        $this->assertEquals(1, $recipients[1]->friends()->count());
        $this->assertEquals(0, $recipients[2]->friends()->count());
        $this->assertEquals(0, $recipients[3]->friends()->count());

        $this->containsOnlyInstancesOf(\App\User::class, $sender->friends());
    }

    /** @test */
    public function it_returns_friend_requests_from_user()
    {
        $sender = factory(User::class)->create();
        $recipients = factory(User::class, 3)->create();

        foreach ($recipients as $recipient) {
            $sender->addFriend($recipient);
        }

        $recipients[0]->acceptFriend($sender);

        $this->assertEquals(2, $sender->friendRequestsSent()->count());

        $this->containsOnlyInstancesOf(\App\User::class, $sender->friendRequestsSent());
    }

    /** @test */
    public function it_returns_friend_requests_to_user()
    {
        $recipient = factory(User::class)->create();
        $senders = factory(User::class, 3)->create();

        foreach ($senders as $sender) {
            $sender->addFriend($recipient);
        }

        $recipient->acceptFriend($senders[0]);

        $this->assertEquals(2, $recipient->friendRequestsReceived()->count());

        $this->containsOnlyInstancesOf(\App\User::class, $recipient->friendRequestsReceived());
    }
    /** @test */
    public function it_returns_mutual_friends()
    {
        // create users
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        $user5 = factory(User::class)->create();
        $user6 = factory(User::class)->create();

        // user one add friends
        $user1->addFriend($user2);
        $user2->acceptFriend($user1);
        $user1->addFriend($user3);
        $user3->acceptFriend($user1);
        $user1->addFriend($user4);
        $user4->acceptFriend($user1);
        $user1->addFriend($user6);
        $user6->acceptFriend($user1);

        // user two add friends
        $user2->addFriend($user1);
        $user1->acceptFriend($user2);
        $user2->addFriend($user3);
        $user3->acceptFriend($user2);
        $user2->addFriend($user5);
        $user5->acceptFriend($user2);
        $user2->addFriend($user6);
        $user6->acceptFriend($user2);

        $this->assertEquals(2, $user1->mutualFriendsCount($user2));
        $this->assertCount(2, $user1->mutualFriends($user2));
        $this->assertEquals([$user3->toArray(), $user6->toArray()], $user1->mutualFriends($user2)->toArray());
    }
}
