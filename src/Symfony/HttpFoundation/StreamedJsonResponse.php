<?php

declare(strict_types=1);

namespace App\Symfony\HttpFoundation;

class StreamedJsonResponse extends \Symfony\Component\HttpFoundation\StreamedJsonResponse
{
    use ChunkedResponseTrait;
}
