# printess-api-php

## Getting started ##

Initializing the Printess API client, and setting your API key.

```php
$printess = new \Printess\Api\PrintessApiClient();
$printess->setApiKey("dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
``` 

Creating a new directory.

```php
$directory = $printess->directories->create([
    "parentId" => 46494203,
    "name" => "veniam ipsum consectetur eiusmod ex",
]);
```
