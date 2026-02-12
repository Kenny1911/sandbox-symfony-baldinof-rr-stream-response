<?php

declare(strict_types=1);

namespace App\Symfony\HttpFoundation;

class StreamedResponse extends \Symfony\Component\HttpFoundation\StreamedResponse
{
    use ChunkedResponseTrait;
}
