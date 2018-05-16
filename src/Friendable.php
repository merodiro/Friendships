<?php

namespace Merodiro\Friendships;

trait Friendable
{
    public function checkFriendship($user)
    {
        if ($this->id == $user->id) {
            return 'same_user';
        }

        $friendship = Friendship::betweenUsers($this, $user)
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
            Friendship::create([
                    'requester'      => $this->id,
                    'user_requested' => $recipient->id,
                ]);
            event('friendrequest.sent', [$this, $recipient]);
        }

        return $friendshipStatus == 'not_friends';
    }

    public function acceptFriend($sender)
    {
        $friendshipStatus = $this->checkFriendship($sender);

        if ($friendshipStatus == 'pending') {
            Friendship::betweenUsers($this, $sender)
                ->update(['status' => 1]);
            event('friendrequest.accepted', [$this, $sender]);
        }

        return $friendshipStatus == 'pending';
    }

    public function deleteFriend($user)
    {
        $friendshipStatus = $this->checkFriendship($user);

        if ($friendshipStatus != 'not_friends') {
            Friendship::betweenUsers($this, $user)
                ->delete();
            event('friendship.deleted', [$this, $user]);
        }

        return $friendshipStatus != 'not_friends';
    }

    public function friends_ids()
    {
        $friendsIds = Friendship::where(function ($query) {
            $query->whereSender($this);
        })->orWhere(function ($query) {
            $query->whereRecipient($this);
        })->accepted(1)->get(['user_requested', 'requester'])->toArray();

        $friendsIds = collect($friendsIds)->flatten()->unique()->reject(function ($id) {
            return $id == $this->id;
        });
        return $friendsIds;
    }

    public function friends()
    {
        $friendsIds = $this->friends_ids();

        return static::whereIn('id', $friendsIds)
            ->distinct()
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

    public function mutualFriends($user)
    {
        $userFriends = $user->friends_ids();
        $friends = $this->friends_ids();

        $mutualIds = $userFriends->intersect($friends);

        return static::whereIn('id', $mutualIds)->get();
    }
}
