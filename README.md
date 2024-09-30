# ViewStats API Wrapper

This PHP wrapper provides an easy-to-use interface for interacting with the ViewStats API. It handles authentication, request sending, and response processing, including decryption of encrypted responses.

## Features

- Simple interface for making GET and POST requests to the ViewStats API
- Automatic handling of authentication tokens
- Built-in error handling and logging
- Decryption of encrypted API responses
- Easy integration into existing PHP projects

## Requirements

- PHP 7.4 or higher
- cURL extension
- OpenSSL extension

## Installation

You can install this package via Composer:

```bash
composer require peranetwork/viewstats-api
```

## Usage

### Basic usage

```php
use ViewStatsWrapper\ViewStatsAPI;

$apiToken = '32ev9m0qggn227ng1rgpbv5j8qllas8uleujji3499g9had6oj7f0ltnvrgi00cq';
$api = new ViewStatsAPI($apiToken);

try {
    $popularChannels = $api->get('/channels/popular', ['total' => 1]);
    print_r($popularChannels);
} catch (\ViewStatsWrapper\Exceptions\APIException $e) {
    echo "API Error: " . $e->getMessage();
} catch (\ViewStatsWrapper\Exceptions\DecryptionException $e) {
    echo "Decryption Error: " . $e->getMessage();
}
```

### Making GET requests

```php
$params = ['param1' => 'value1', 'param2' => 'value2'];
$response = $api->get('/endpoint', $params);
```

### Making POST requests

```php
$data = ['key1' => 'value1', 'key2' => 'value2'];
$response = $api->post('/endpoint', $data);
```

## Error Handling

The wrapper throws two types of exceptions:

- `APIException`: For general API errors, including HTTP errors.
- `DecryptionException`: Specifically for errors during the decryption of API responses.

It's recommended to catch these exceptions separately to handle different types of errors appropriately.

## Logging

The wrapper includes a basic logging mechanism. Logs are written to `debug.log` in the project root. You can customize the logging behavior by modifying the `Logger` class.

## Security

- API tokens are sent securely in the request headers.
- Encrypted responses are automatically decrypted using AES-256-GCM.
- Sensitive data like API tokens should be stored securely and not hard-coded in your application.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

[MIT License](LICENSE.md)

## Support

If you encounter any problems or have any questions, please open an issue on the GitHub repository.
