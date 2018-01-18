<?php

namespace Merodiro\Friendships;

use Event;

trait Friendable
{
    public function checkFriendship($user)
    {
        if ($this->id == $user->id) {
            return 'same_user';
        }

        $friendship = Friendship::betweenModels($this, $user)
            ->first();

        if (!$friendship) {
            return 'not_friends';
        }

        if ($friendship->status == 1) {
            return 'friends';
        }
        if ($friendship->requester == $this->id) {
            return 'waiting';
        }
        if ($friendship->user_requested == $this->id) {
            return 'pending';
        }
    }

    public function addFriend($recipient)
    {
        $friendshipStatus = $this->checkFriendship($recipient);

        if ($friendshipStatus == 'not_friends') {
            Event::fire('friendrequest.sent', [$this, $recipient]);

            return Friendship::create([
                'requester'      => $this->id,
                'user_requested' => $recipient->id,
            ]);
        }
    }

    public function acceptFriend($sender)
    {
        $friendshipStatus = $this->checkFriendship($sender);

        if ($friendshipStatus == 'pending') {
            Event::fire('friendrequest.accepted', [$this, $sender]);

            return $friendship = Friendship::betweenModels($this, $sender)
                ->update([
                        'status' => 1,
                    ]);
        }
    }

    public function deleteFriend($user)
    {
        Event::fire('friendship.deleted', [$this, $user]);

        return Friendship::betweenModels($this, $user)
            ->delete();
    }

    public function friends()
    {
        $recipients = Friendship::whereSender($this)
            ->accepted(1)
            ->get(['user_requested'])
            ->toArray();

        $senders = Friendship::whereRecipient($this)
            ->accepted(1)
            ->get(['requester'])
            ->toArray();

        $friendsIds = array_merge($recipients, $senders);

        return static::whereIn('id', $friendsIds)
            ->get();
    }

    public function friendRequestsReceived()
    {
        $senders = Friendship::whereRecipient($this)
            ->accepted(0)
            ->get(['requester'])
            ->toArray();

        return static::whereIn('id', $senders)
            ->get();
    }

    public function friendRequestsSent()
    {
        $recipients = Friendship::whereSender($this)
            ->accepted(0)
            ->get(['user_requested'])
            ->toArray();

        return static::whereIn('id', $recipients)
            ->get();
    }

    public function isFriendsWith($user)
    {
        $friendshipStatus = $this->checkFriendship($user);

        return $friendshipStatus === 'friends';
    }
}
