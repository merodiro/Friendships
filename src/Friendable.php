<?php

namespace Merodiro\Friendships;

use Merodiro\Friendships\Exceptions\AddFriendFailed;
use Merodiro\Friendships\Exceptions\AcceptFriendFailed;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait Friendable
{
    public function checkFriendship($user): string
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

    public function addFriend($recipient): void
    {
        $friendshipStatus = $this->checkFriendship($recipient);

        if ($friendshipStatus === 'NOT_FRIENDS') {
            $this->friends()->attach($recipient);
            event('friendrequest.sent', [$this, $recipient]);
        } else {
            throw new AddFriendFailed($friendshipStatus);
        }
    }

    public function acceptFriend($sender): void
    {
        $friendshipStatus = $this->checkFriendship($sender);

        if ($friendshipStatus == 'PENDING') {
            $this->friends()->attach($sender, ['status' => 1]);
            $sender->friends()->updateExistingPivot($this, ['status' => 1]);

            event('friendrequest.accepted', [$this, $sender]);
        } else {
            throw new AcceptFriendFailed($friendshipStatus);
        }
    }

    public function deleteFriend($user): void
    {
        $friendshipStatus = $this->checkFriendship($user);

        if ($friendshipStatus != 'NOT_FRIENDS') {
            $this->friends()->detach($user);
            $user->friends()->detach($this);

            event('friendship.deleted', [$this, $user]);
        }
    }

    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(config('friendships.user_model'), 'friendships', 'user_id', 'friend_id')
            ->where('status', 1);
    }

    public function friendRequestsReceived(): BelongsToMany
    {
        return $this->belongsToMany(config('friendships.user_model'), 'friendships', 'friend_id', 'user_id')
            ->where('status', 0);
    }

    public function friendRequestsSent(): BelongsToMany
    {
        return $this->belongsToMany(config('friendships.user_model'), 'friendships', 'user_id', 'friend_id')
            ->where('status', 0);
    }

    public function isFriendsWith($user): bool
    {
        return $this->friends->contains($user);
    }

    public function mutualFriendsCount($user): int
    {
        $userFriends = $user->friends->pluck('id');
        $friends = $this->friends->pluck('id');

        return $userFriends->intersect($friends)->count();
    }

    public function mutualFriends($user): Collection
    {
        $userFriends = $user->friends->pluck('id');
        $friends = $this->friends->pluck('id');

        $mutualIds = $userFriends->intersect($friends);

        return static::whereIn('id', $mutualIds)->get();
    }
}
