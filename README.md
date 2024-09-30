# ViewStats API Wrapper

This PHP wrapper provides an easy-to-use interface for interacting with the ViewStats API. It handles authentication, request sending, and response processing, including decryption of encrypted responses.

ViewStats is a platform for tracking and analyzing YouTube channel statistics. The API provides access to various data points, including channel information, video statistics, and more.

Address: [ViewStats](https://viewstats.com)

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
require 'vendor/autoload.php';

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

## API Endpoints

Here are some example endpoints you can use with this wrapper:

### GET Requests

1. Channel Information:
   ```php
   $api->get('/channels/@delba');
   ```

2. Channel About Me:
   ```php
   $api->get('/channels/@delba/aboutMe');
   ```

3. Channel Stats:
   ```php
   $api->get('/channels/@delba/stats', [
       'range' => '28',
       'groupBy' => 'daily',
       'sortOrder' => 'ASC',
       'withRevenue' => 'true',
       'withEvents' => 'true',
       'withBreakdown' => 'false',
       'withToday' => 'false'
   ]);
   ```

4. All-time Channel Stats:
   ```php
   $api->get('/channels/@delba/stats', [
       'range' => 'alltime',
       'groupBy' => 'daily',
       'sortOrder' => 'ASC',
       'withRevenue' => 'true',
       'withEvents' => 'false',
       'withBreakdown' => 'false'
   ]);
   ```

5. Monthly Channel Stats:
   ```php
   $api->get('/channels/@delba/stats', [
       'range' => '365',
       'groupBy' => 'monthly',
       'sortOrder' => 'ASC',
       'withRevenue' => 'true',
       'withEvents' => 'true',
       'withBreakdown' => 'false',
       'withToday' => 'false'
   ]);
   ```

6. Long and Short Videos:
   ```php
   $api->get('/channels/@delba/longsAndShorts');
   ```

7. Featured Video:
   ```php
   $api->get('/channels/@delba/featuredVideo');
   ```

8. Channel Averages:
   ```php
   $api->get('/channels/@delba/averages');
   ```

9. Channel Videos:
   ```php
   $api->get('/channels/@delba/videos', [
       'orderBy' => 'uploadDate',
       'limit' => '40',
       'offset' => '0',
       'withThumbnail' => 'true',
       'type' => 'lf'
   ]);
   ```

10. Channel Projections:
    ```php
    $api->get('/channels/@delba/projections/milestones');
    ```

11. Similar Channels:
    ```php
    $api->get('/channels/@delba/similar');
    ```

12. Popular Channels:
    ```php
    $api->get('/channels/popular', ['total' => '6']);
    ```

### POST Requests

1. Channel Rankings:
   ```php
   $api->post('/rankings/channels', [
       'interval' => 'weekly',
       'sortBy' => 'subs',
       'made_for_kids' => true,
       'show_music' => true,
       'category_id' => null,
       'country' => null,
       'show_movies' => true
   ]);
   ```

2. Video Rankings:
   ```php
   $api->post('/rankings/videos', [
       'interval' => 7,
       'includeKids' => true,
       'includeMusic' => true,
       'country' => 'all',
       'categoryId' => 9999,
       'videoFormat' => 'all'
   ]);
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
