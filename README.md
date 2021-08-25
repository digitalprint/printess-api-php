# printess-api-php

## Getting started ##

Initializing the Printess API client, and setting your AccessToken key.

```php
$printess = new \Printess\Api\PrintessApiClient();
$printess->setAccessToken("TheVerySecretAccessToken");
``` 

Creating a new produce job.

```php
$job = $printess->production->produce([
            'templateName' => 'st:thesavedjobtoken',
            'outputSettings' => ['dpi' => 150],
            'outputFiles' => [
                [ 'documentName' => 'myDocument' ],
            ],
            'origin' => 'printess',
        ]);
```


Get the job status with the link to the pdf file.

```php
$status = $printess->production->getStatus([
        "jobId" => $job->jobId,
    ]);
```
