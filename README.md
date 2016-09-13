# jaczkog/json-rpc: PHP JSON-RPC client

[![Latest Version on Packagist][icon-version]][link-packagist]
[![Total Downloads][icon-downloads]][link-packagist]
[![Build Status][icon-travis]][link-travis]
[![Code Coverage][icon-codecov]][link-codecov]

[icon-version]: https://img.shields.io/packagist/v/jaczkog/json-rpc.svg?style=plastic
[icon-downloads]: https://img.shields.io/packagist/dt/jaczkog/json-rpc.svg?style=plastic
[icon-travis]: https://img.shields.io/travis/jaczkog/php-json-rpc/master.svg?style=plastic
[icon-codecov]: https://img.shields.io/codecov/c/github/jaczkog/php-json-rpc/master.svg?style=plastic

[link-packagist]: https://packagist.org/packages/jaczkog/json-rpc
[link-travis]: https://travis-ci.org/jaczkog/php-json-rpc
[link-codecov]: https://codecov.io/gh/jaczkog/php-json-rpc

## Setup

```bash
composer require jaczkog/json-rpc
```

## Usage

```php
use JsonRpc\JsonRpcClient;

$jsonRpcClient = new JsonRpcClient('rpc-server:8080', JsonRpcClient::VERSION_1);
$response      = $jsonRpcClient->sendRequest('method_name', ['param1' => 1, 'param2' => true]);

if ($response->isSuccess()) {
    echo $response->result;
} else {
    echo sprintf('ERROR: %s (%d)', $response->error->message, $response->error->code);
}
```
