## How to use events

for simplicity I will use events subscriber

add the following in `EventServiceProvider.php`

```php
protected $subscribe = [
    'App\Listeners\FriendshipsSubscriber',
];
```

then in `FriendshipsSubscriber.php`

```php
class FriendshipsSubscriber implements ShouldQueue
{
  ...
  public function subscribe($events)
  {
      $events->listen(
          'friendrequest.sent',
          'App\Listeners\FriendshipsSubscriber@onFriendRequestSent'
      );

      $events->listen(
          'friendrequest.accepted',
          'App\Listeners\FriendshipsSubscriber@onFriendRequestAccepted'
      );

      $events->listen(
          'friendship.deleted',
          'App\Listeners\FriendshipsSubscriber@onFriendDeleted'
      );
  }
  ...
}

```

then in each method of them

```php
...
public function onFriendRequestSent($sender, $recipient)
{
   ...
}

public function onFriendRequestAccepted($recipient, $sender)
{
    ...
}

public function onFriendDeleted($deleter, $deleted)
{
    ...
}

```