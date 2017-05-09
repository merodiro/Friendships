<?php

namespace Merodiro\Friendships;

use Event;

trait Friendable
{
    public function addFriend($recipient)
    {
        $friendshipStatus = $this->checkFriendship($recipient);

        if ($friendshipStatus == 'not friends') {
            Event::fire('friendrequest.sent', [$this, $recipient]);

            return Friendship::create([
                'requester'      => $this->id,
                'user_requested' => $recipient->id,
            ]);
        }
    }

    public function checkFriendship($user)
    {
        if ($this->id == $user->id) {
            return 'same user';
        }

        $friendship = Friendship::betweenModels($this, $user)
            ->first();

        if (!$friendship) {
            return 'not friends';
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
            ->pluck('user_requested')
            ->all();
        $senders = Friendship::whereRecipient($this)
            ->accepted(1)
            ->pluck('requester')
            ->all();

        $friendsIds = array_merge($recipients, $senders);

        return static::whereIn('id', $friendsIds)
            ->get();
    }

    public function friendRequestFrom()
    {
        $senders = Friendship::whereRecipient($this)
            ->accepted(0)
            ->pluck('requester')
            ->all();

        return static::whereIn('id', $senders)
            ->get();
    }

    public function friendRequestTo()
    {
        $recipients = Friendship::whereSender($this)
            ->accepted(0)
            ->pluck('user_requested')
            ->all();

        return static::whereIn('id', $recipients)
            ->get();
    }
}
