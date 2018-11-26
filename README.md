# Laravel 5 Friendships

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Build status][ico-appveyor]][link-appveyor]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package gives users the ability to manage their friendships.

## Models can:

-   Send Friend Requests
-   Accept Friend Requests
-   Deny Friend Requests
-   Delete Friend

## Installation

First, install the package through Composer.

```php
composer require merodiro/friendships
```

Then include the service provider inside `config/app.php`.

```php
'providers' => [
    ...
    Merodiro\Friendships\FriendshipsServiceProvider::class,
    ...
];
```

Finally, migrate the database

```
php artisan migrate
```

## Setup a Model

```php
use Merodiro\Friendships\Friendable;
class User extends Model
{
    use Friendable;
    ...
}
```

## How to use

[Check the Test file to see the package in action](https://github.com/merodiro/Friendships/blob/master/tests/FriendshipsTest.php)

#### Send a Friend Request

```php
$user->addFriend($recipient);
```

#### Accept a Friend Request

```php
$user->acceptFriend($sender);
```

#### Deny a Friend Request

```php
$user->deleteFriend($sender);
```

#### Remove Friend

```php
$user->deleteFriend($friend);
```

#### Mutual Friends

```php
$user->mutualFriends($anotherUser);
```

#### Mutual Friends Count

```php
$user->mutualFriendsCount($anotherUser);
```

#### check the current relationship between two users

```php
$user->checkFriendship($anotherUser);
```

it returns

-   `same_user` => if the `$user` is checking his own account
-   `friends` => if they are friends
-   `waiting` => if `$user` sent a request waiting for approval from `$anotherUser`
-   `pending` => if `$anotherUser` user sent a request waiting for approval from `$user`
-   `not_friends` => if they are not friends

#### Check if two users are friends

```php
$user->isFriendsWith($anotherUser);
```

it returns `true` if they are friends and `false` if they aren't

## Friends

the following ways to access friends are using Eloquent relationships
so you can use the following:
you can use any Eloquent method on them like `where()`, 'take()` and any other method

#### Get Friends

```php
$user->friends;
$user->friends()->take(5)->get();
User::with('friends')->get();
```

#### Get access users that `$user` has received friend requests from

```php
$user->friendRequestsReceived;
$user->friendRequestsReceived()->take(5)->get();
User::with('friendRequestsReceived')->get();
```

#### Get access users that `$user` has sent friend requests to

```php
$user->friendRequestsSent;
$user->friendRequestsSent()->take(5)->get();
User::with('friendRequestsSent')->get();
```

## Events

This is the list of the events fired by default for each action

|       Event name       |               Fired               |
| :--------------------: | :-------------------------------: |
|   friendrequest.sent   |   When a friend request is sent   |
| friendrequest.accepted | When a friend request is accepted |
|   friendship.deleted   |  When a friend request is denied  |
|   friendship.deleted   |   When a friendship is deleted    |

for more about how to use the events
[Check this example](/Events.md)

## Testing

```bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security-related issues, please email merodiro@gmail.com instead of using the issue tracker.

## Credits

-   [Amr A. Mohammed][link-author]
-   [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/merodiro/friendships.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/merodiro/Friendships/master.svg?style=flat-square
[ico-appveyor]: https://ci.appveyor.com/api/projects/status/6cio9isdnhmdxl8r?svg=true
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/merodiro/Friendships.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/merodiro/Friendships.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/merodiro/friendships.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/merodiro/friendships
[link-travis]: https://travis-ci.org/merodiro/Friendships
[link-appveyor]: https://ci.appveyor.com/project/merodiro/friendships
[link-scrutinizer]: https://scrutinizer-ci.com/g/merodiro/Friendships/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/merodiro/Friendships
[link-downloads]: https://packagist.org/packages/merodiro/friendships
[link-author]: https://github.com/merodiro
[link-contributors]: ../../contributors
