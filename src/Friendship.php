<?php

namespace Merodiro\Friendships;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    protected $fillable = ['requester', 'user_requested', 'status'];

    public function scopeWhereSender($query, $model)
    {
        return $query->where('requester', $model->getKey());
    }

    public function scopeWhereRecipient($query, $model)
    {
        return $query->where('user_requested', $model->getKey());
    }

    public function scopeAccepted($query, $val)
    {
        return $query->where('status', $val);
    }

    public function scopeBetweenModels($query, $sender, $recipient)
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
