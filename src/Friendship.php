<?php

namespace Merodiro\Friendships;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    protected $fillable = ['user_id', 'friend_id', 'status'];

    public function scopeWhereSender($query, $model)
    {
        return $query->where('user_id', $model->getKey());
    }

    public function scopeWhereRecipient($query, $model)
    {
        return $query->where('friend_id', $model->getKey());
    }

    public function scopeAccepted($query, $val)
    {
        return $query->where('status', $val);
    }

    public function scopeBetweenUsers($query, $sender, $recipient)
    {
        $query->where(function ($queryIn) use ($sender, $recipient) {
            $queryIn->where(function ($q) use ($sender, $recipient) {
                $q->whereSender($sender)->whereRecipient($recipient);
            })->orWhere(function ($q) use ($sender, $recipient) {
                $q->whereSender($recipient)->whereRecipient($sender);
            });
        });
    }
}
