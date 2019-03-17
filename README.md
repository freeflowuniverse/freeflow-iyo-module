## Itsyou.online Integration

- Move the file `IYO.php` to `{Humhub_DIR}/protected/humhub/modules/user/authclient/IYO.php`
- Add the following section into `protected/config/common.php`

```php
return [
    'components' => [
        'authClientCollection' => [
            'clients' => [
                'itsyouonline' => [
                    'class' => 'humhub\modules\user\authclient\IYO',
                    'clientId' => getenv('CLIENT_ID'),
                    'clientSecret' => getenv('CLIENT_SECRET'),
                ],
            ],
        ]  
    ]
];
```
