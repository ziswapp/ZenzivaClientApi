[![Build Status](https://travis-ci.org/Ziswapp/ZenzivaClientApi.svg?branch=master)](https://travis-ci.org/Ziswapp/ZenzivaClientApi)
[![codecov](https://codecov.io/gh/Ziswapp/ZenzivaClientApi/branch/master/graph/badge.svg)](https://codecov.io/gh/Ziswapp/ZenzivaClientApi)
[![Latest Stable Version](https://poser.pugx.org/ziswapp/zenziva-client-api/v/stable)](https://packagist.org/packages/ziswapp/zenziva-client-api)
[![License](https://poser.pugx.org/ziswapp/zenziva-client-api/license)](https://packagist.org/packages/ziswapp/zenziva-client-api)
[![Total Downloads](https://poser.pugx.org/ziswapp/zenziva-client-api/downloads)](https://packagist.org/packages/ziswapp/zenziva-client-api)

# ZenzivaClientApi

Ini adalah client api untuk zenziva sms gateway, sementara ini hanya support untuk SMS Center saja.

# Install

```
composer require ziswapp/zenziva-client-api
```

# Penggunaan

```php

<?php

use Carbon\Carbon;
use Ziswapp\Zenziva\Credential;
use Ziswapp\Zenziva\Response\Credit;
use Ziswapp\Zenziva\Client\SmsCenter;

require_once __DIR__ . '/vendor/autoload.php';

$url = '';
$key = '';
$secret = '';

$credential = new Credential($url, $key, $secret);

$client = new SmsCenter($credential, Symfony\Component\HttpClient\HttpClient::create());

// Check credit balance, will be return Ziswapp\Zenziva\Response\Credit
$credit = $client->balance();

$now = Carbon::now();

$startDate = $now->clone()->subDay();
$endDate = $now->clone()->addDay();

// Get list of inbox by date, will be return array Ziswapp\Zenziva\Response\Inbox
$client->inbox($startDate, $endDate);

// Get list of outbox by date, will be return array Ziswapp\Zenziva\Response\Outbox
$client->outbox($startDate, $endDate);

// Send sms message, will be return Ziswapp\Zenziva\Response\Outbox
$client->send('081318788271', 'Testing SMS');

// Check status pengiriman sms, wil be return Ziswapp\Zenziva\Response\Outbox
$client->status('messageId');
```
