<?php

namespace Merodiro\Friendships;

trait Friendable
{
    public function checkFriendship($user)
    {
        if ($this->id == $user->id) {
            return 'same_user';
        }

        $friendship = Friendship::betweenUsers($this, $user);

        if ($friendship->count() == 0) {
            return 'not_friends';
        } elseif ($friendship->count() == 2) {
            return 'friends';
        } elseif ($friendship->first()->user_id == $this->id) {
            return 'waiting';
        } else {
            return 'pending';
        }
    }

    public function addFriend($recipient)
    {
        $friendshipStatus = $this->checkFriendship($recipient);

        if ($friendshipStatus == 'not_friends') {
            Friendship::create([
                'user_id' => $this->id,
                'friend_id' => $recipient->id,
            ]);
            event('friendrequest.sent', [$this, $recipient]);
        }

        return $friendshipStatus == 'waiting';
    }

    public function acceptFriend($sender)
    {
        $friendshipStatus = $this->checkFriendship($sender);

        if ($friendshipStatus == 'pending') {
            Friendship::create([
                'user_id' => $this->id,
                'friend_id' => $sender->id,
                'status' => 1
            ]);
            Friendship::betweenUsers($this, $sender)
                ->update(['status' => 1]);
            event('friendrequest.accepted', [$this, $sender]);
        }

        return $friendshipStatus == 'friends';
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

    public function friends()
    {
        return $this->belongsToMany(config('friendships.user_model'), 'friendships', 'user_id', 'friend_id')
            ->where('status', 1);
    }

    public function friendRequestsReceived()
    {
        return $this->belongsToMany(config('friendships.user_model'), 'friendships', 'friend_id', 'user_id')
            ->where('status', 0);
    }

    public function friendRequestsSent()
    {
        return $this->belongsToMany(config('friendships.user_model'), 'friendships', 'user_id', 'friend_id')
            ->where('status', 0);
    }

    public function isFriendsWith($user)
    {
        $friendshipStatus = $this->checkFriendship($user);

        return $friendshipStatus === 'friends';
    }

    public function mutualFriendsCount($user)
    {
        $userFriends = $user->friends->pluck('id');
        $friends = $this->friends->pluck('id');

        return $userFriends->intersect($friends)->count();
    }

    public function mutualFriends($user)
    {
        $userFriends = $user->friends->pluck('id');
        $friends = $this->friends->pluck('id');

        $mutualIds = $userFriends->intersect($friends);

        return static::whereIn('id', $mutualIds)->get();
    }
}
