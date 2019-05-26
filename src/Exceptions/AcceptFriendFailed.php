<?php

namespace Merodiro\Friendships\Exceptions;

use Exception;

class AcceptFriendFailed extends Exception
{
    public function __construct(string $status) {
        switch ($status) {
            case 'SAME_USER':
                $this->message = 'user can not accept friend request from himself';
                break;
            case 'WAITING':
                $this->message = 'user can not accept a friend request he sent';
                break;
            case 'NOT_FRIENDS':
                $this->message = 'user has no pending friend request from this user';
                break;
            case 'FRIENDS':
                $this->message = 'users are already friends';
                break;
        }
    }
}
