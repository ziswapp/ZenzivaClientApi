[![Build Status](https://travis-ci.org/Ziswapp/ZenzivaClientApi.svg?branch=master)](https://travis-ci.org/Ziswapp/ZenzivaClientApi)
[![codecov](https://codecov.io/gh/Ziswapp/ZenzivaClientApi/branch/master/graph/badge.svg)](https://codecov.io/gh/Ziswapp/ZenzivaClientApi)
[![Latest Stable Version](https://poser.pugx.org/ziswapp/zenziva-client-api/v/stable)](https://packagist.org/packages/ziswapp/zenziva-client-api)
[![License](https://poser.pugx.org/ziswapp/zenziva-client-api/license)](https://packagist.org/packages/ziswapp/zenziva-client-api)
[![Total Downloads](https://poser.pugx.org/ziswapp/zenziva-client-api/downloads)](https://packagist.org/packages/ziswapp/zenziva-client-api)

# ZenzivaClientApi

Ini adalah client api untuk zenziva sms gateway.

# Install

```
composer require ziswapp/zenziva-client-api
```

# Penggunaan

```php

<?php

use Ziswapp\Zenziva\ClientFactory;
use Symfony\Component\HttpClient\HttpClient;

$url = '';
$key = '';
$secret = '';

$httpClient = HttpClient::create();

// Crete client
$regular = ClientFactory::regular($httpClient, $key, $secret); // Regular client
$masking = ClientFactory::masking($httpClient, $key, $secret); // Masking client
$otp = ClientFactory::otp($httpClient, $key, $secret); // Masking with otp client
$smsCenter = ClientFactory::center($httpClient, $key, $secret, $url); // SMS Center client

// Alternative
$regular = ClientFactory::make($httpClient, ClientFactory::TYPE_REGULAR, $key, $secret); // Regular client
$masking = ClientFactory::make($httpClient, ClientFactory::TYPE_MASKING, $key, $secret); // Masking client
$otp = ClientFactory::make($httpClient, ClientFactory::TYPE_MASKING_OTP, $key, $secret); // Masking with otp client
$smsCenter = ClientFactory::make($httpClient, ClientFactory::TYPE_SMS_CENTER, $key, $secret, $url); // SMS Center client

// Zenziva Regular Operation
$httpClient = HttpClient::create();
$regular = ClientFactory::make($httpClient, ClientFactory::TYPE_REGULAR, $key, $secret); // Regular client
$regular->send('081318788271', 'Sending notification.'); // Return array

// Zenziva Masking Operation
$httpClient = HttpClient::create();
$masking = ClientFactory::make($httpClient, ClientFactory::TYPE_MASKING, $key, $secret); // Masking client
$masking->balance(); // Check balance return Credit object
$masking->send('081318788271', 'Sending notification.'); // Return Outbox object
$masking->setIsOtp(true); // Change to masking otp client

// Zenziva SMS Center Operation
$smsCenter = ClientFactory::make($httpClient, ClientFactory::TYPE_SMS_CENTER, $key, $secret, $url); // SMS Center client
$smsCenter->balance(); // Check balance return Credit object, will be throw CreditExpiredException or CreditLimitException if balance is 0 and expired date < now
$smsCenter->send('081318788271', 'Sending notification.'); // Return Outbox object
$smsCenter->outbox(new DateTime(), new DateTime()); // Get outbox by date, return array Outbox object
$smsCenter->inbox(new DateTime(), new DateTime()); // Get inbox by date, return array Inbox object
$smsCenter->status('messageId'); // Get status sms by messageId, return Outbox object
```
