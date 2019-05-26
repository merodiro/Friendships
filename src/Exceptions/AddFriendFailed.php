<?php

namespace Merodiro\Friendships\Exceptions;

use Exception;

class AddFriendFailed extends Exception
{
    public function __construct(string $status) {
        switch ($status) {
            case 'SAME_USER':
                $this->message = 'user can not add himself as a friend';
                break;
            case 'WAITING':
                $this->message = 'user already sent a friend request to this user';
                break;
            case 'PENDING':
                $this->message = 'user has a pending friend request from this user';
                break;
            case 'FRIENDS':
                $this->message = 'users are already friends';
                break;
        }
    }
}
