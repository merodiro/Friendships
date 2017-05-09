<?php

namespace Merodiro\Friendships;

trait Friendable
{
    public function addFriend($user)
    {
        $friendshipStatus = $this->checkFriendship($user);

        if ($friendshipStatus == 'not friends') {
            return Friendship::create([
                'requester'      => $this->id,
                'user_requested' => $user->id,
            ]);
        }

        return 0;
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
        } elseif ($friendship->status == 1) {
            return 'friends';
        } elseif ($friendship->requester == $this->id) {
            return 'waiting';
        } elseif ($friendship->user_requested == $this->id) {
            return 'pending';
        }
    }

    public function acceptFriend($user)
    {
        $friendshipStatus = $this->checkFriendship($user);

        if ($friendshipStatus == 'pending') {
            return $friendship = Friendship::betweenModels($this, $user)
                ->update([
                        'status' => 1,
                    ]);
        }
    }

    public function deleteFriend($user)
    {
        Friendship::betweenModels($this, $user)
            ->delete();

        return 1;
    }

    public function friends()
    {
        $recipients = Friendship::whereSender($this)->accepted(1)->pluck('user_requested')->all();
        $senders = Friendship::whereRecipient($this)->accepted(1)->pluck('requester')->all();

        $friendsIds = array_merge($recipients, $senders);

        return static::whereIn('id', $friendsIds)->get();
    }

    public function friendRequestFrom()
    {
        $senders = Friendship::whereRecipient($this)->accepted(0)->pluck('requester')->all();

        return static::whereIn('id', $senders)->get();
    }

    public function friendRequestTo()
    {
        $recipients = Friendship::whereSender($this)->accepted(0)->pluck('user_requested')->all();

        return static::whereIn('id', $recipients)->get();
    }
}
