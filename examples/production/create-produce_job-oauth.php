<?php
/*
 * Example 1 -  Using OAuth access token to prepare a produce job
 */

use Printess\Api\Exceptions\ApiException;
use Printess\Api\PrintessApiClient;

try {
    /*
     * Initialize the Printess API library with your OAuth access token.
     */

    /** @var PrintessApiClient $printess */
    $printess = require "../initialize_with_oauth.php";


    /*
     * Generate a unique order id for this example.
     */
    $orderId = time();

    /**
     * Parameters for creating a produce job via oAuth
     *
     * @See https://api.printess.com/swagger/index.html#operations-tag-Production
     */
    $job = $printess->production->produce([
        'templateName' => 'poster_p',
        'origin' => 'flip',
        'meta' => '{"name":"Lutz Bickers"}',
        'outputSettings' => [
            'dpi' => 300,
        ],
        'vdp' => [
            'form' => [
                'text1' => 'Simon & Garfunkel',
                'text2' => 'Bridge over Troubled Water',
                'image1' => 'https://images.unsplash.com/photo-1623680904963-5580d963e18e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=2134&q=80',
                'backgroundColor' => 'Dunkelblau',
                'remote' => 'ON',
                'DOCUMENT_SIZE' => '29.7x42_118315h',
            ],
        ],
    ]);

    /*
     * In this example we store the jobId in a database.
     */
    database_write($orderId, $job->jobId);

    sleep(3);

    $status = $printess->production->getStatus([
        "jobId" => $job->jobId,
    ]);

    echo '<a target="_blank" href="' . $status->result->r->preview . '">Pdf-File</a>';

} catch (ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
