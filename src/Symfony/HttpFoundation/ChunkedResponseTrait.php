<?php

declare(strict_types=1);

namespace App\Symfony\HttpFoundation;

trait ChunkedResponseTrait
{
    protected const int DEFAULT_CHUNK_SIZE = 1024 * 16; // 16Kb

    protected int $chunkSize = self::DEFAULT_CHUNK_SIZE;

    public function setChunkSize(int $chunkSize): static
    {
        if ($chunkSize < 1) {
            throw new \InvalidArgumentException('The chunk size of a BinaryFileResponse cannot be less than 1.');
        }

        $this->chunkSize = $chunkSize;

        return $this;
    }

    public function sendContent(): static
    {
        $content = '';
        ob_start(function (string $buffer, int $phase) use (&$content) {
            $content .= $buffer;

            if (strlen($content) >= $this->chunkSize) {
                $chunk = $content;
                $content = '';

                return $chunk;
            }

            if (($phase & PHP_OUTPUT_HANDLER_END) && '' !== $content) {
                return $content;
            }

            return '';
        }, $this->chunkSize);

        parent::sendContent();

        @ob_get_flush();
        flush();

        return $this;
    }
}
