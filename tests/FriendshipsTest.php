<?php

use Merodiro\Friendships\Friendship;

class FriendshipTest extends TestCase
{

    /** @test */
    public function user_can_send_a_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);

        $this->assertCount(1, $recipient->friendRequestsReceived);
        $this->assertCount(1, $sender->friendRequestsSent);

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

        $this->assertCount(1, $recipient->friendRequestsReceived);
    }

    /** @test */
    public function user_can_send_a_friend_request_if_frienship_is_denied()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);

        $recipient->deleteFriend($sender);
        $this->assertCount(0, $recipient->friendRequestsReceived);


        $sender->addFriend($recipient);
        // reset relationship
        $recipient->setRelations([]);
        $this->assertCount(1, $recipient->friendRequestsReceived);
    }

    /** @test */
    public function user_can_remove_a_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);
        $sender->deleteFriend($recipient);
        $this->assertCount(0, $recipient->friendRequestsReceived);

        $recipient->setRelations([]);

        $sender->addFriend($recipient);
        $this->assertCount(1, $recipient->friendRequestsReceived);

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
        $this->assertCount(0, $user->friendRequestsReceived);
        $this->assertCount(0, $user->friendRequestsSent);
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
    public function user_has_not_friend_request_from_if_he_accepted_the_friend_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->addFriend($recipient);
        $recipient->acceptFriend($sender);

        $this->assertCount(0, $recipient->friendRequestsReceived);
        $this->assertCount(0, $sender->friendRequestsSent);
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

        $this->assertCount(2, $sender->friends);
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

        $this->assertCount(2, $sender->friends);

        $this->assertCount(1, $recipients[0]->friends);
        $this->assertCount(1, $recipients[1]->friends);
        $this->assertCount(0, $recipients[2]->friends);
        $this->assertCount(0, $recipients[3]->friends);

        $this->containsOnlyInstancesOf(\App\User::class, $sender->friends);
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

        $this->assertCount(2, $sender->friendRequestsSent);
        $this->containsOnlyInstancesOf(\App\User::class, $sender->friendRequestsSent);
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

        $this->assertCount(2, $recipient->friendRequestsReceived);
        $this->containsOnlyInstancesOf(\App\User::class, $recipient->friendRequestsReceived);
    }
    /** @test */
    public function it_returns_mutual_friends()
    {
        // create users
        $users = factory(User::class, 6)->create();

        // user one add friends
        $users[0]->addFriend($users[1]);
        $users[1]->acceptFriend($users[0]);
        $users[0]->addFriend($users[2]);
        $users[2]->acceptFriend($users[0]);
        $users[0]->addFriend($users[3]);
        $users[3]->acceptFriend($users[0]);
        $users[0]->addFriend($users[5]);
        $users[5]->acceptFriend($users[0]);

        // user two add friends
        $users[1]->addFriend($users[0]);
        $users[0]->acceptFriend($users[1]);
        $users[1]->addFriend($users[2]);
        $users[2]->acceptFriend($users[1]);
        $users[1]->addFriend($users[4]);
        $users[4]->acceptFriend($users[1]);
        $users[1]->addFriend($users[5]);
        $users[5]->acceptFriend($users[1]);

        $this->assertEquals(2, $users[0]->mutualFriendsCount($users[1]));
        $this->assertCount(2, $users[0]->mutualFriends($users[1]));
        $this->assertEquals([$users[2]->toArray(), $users[5]->toArray()], $users[0]->mutualFriends($users[1])->toArray());
    }
}
