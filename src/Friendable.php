<?php

namespace Merodiro\Friendships;

trait Friendable
{
    public function checkFriendship($user)
    {
        if ($this->id == $user->id) {
            return 'SAME_USER';
        }

        $friendship = Friendship::betweenUsers($this, $user);

        if ($friendship->count() == 0) {
            return 'NOT_FRIENDS';
        } elseif ($friendship->count() == 2) {
            return 'FRIENDS';
        } elseif ($friendship->first()->user_id == $this->id) {
            return 'WAITING';
        } else {
            return 'PENDING';
        }
    }

    public function addFriend($recipient)
    {
        $friendshipStatus = $this->checkFriendship($recipient);

        if ($friendshipStatus == 'NOT_FRIENDS') {
            $this->friends()->attach($recipient);

            event('friendrequest.sent', [$this, $recipient]);

            return 'waiting';
        }
    }

    public function acceptFriend($sender)
    {
        $friendshipStatus = $this->checkFriendship($sender);

        if ($friendshipStatus == 'PENDING') {
            $this->friends()->attach($sender, ['status' => 1]);
            $sender->friends()->updateExistingPivot($this, ['status' => 1]);

            event('friendrequest.accepted', [$this, $sender]);

            return 'friends';
        }
    }

    public function deleteFriend($user)
    {
        $friendshipStatus = $this->checkFriendship($user);

        if ($friendshipStatus != 'NOT_FRIENDS') {
            $this->friends()->detach($user);
            $user->friends()->detach($this);

            event('friendship.deleted', [$this, $user]);

            return 'NOT_FRIENDS';
        }
    }

    public function ban($user)
    {
        $friendshipStatus = $this->checkFriendship($user);


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
        return $this->friends->contains($user);
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
