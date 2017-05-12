<?php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Merodiro\Friendships\Friendable;

class User extends Authenticatable {
    use Friendable;
}