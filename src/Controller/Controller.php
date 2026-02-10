<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class Controller
{
    public function __construct(
        private string $cacheDir,
    ) {}

    #[Route]
    public function streamedResponse(): StreamedResponse
    {
        return new StreamedResponse($this->getItemsString());
    }

    #[Route(path: '/echo')]
    public function streamedResponseUsedEcho(): StreamedResponse
    {
        return new StreamedResponse(function(): void {
            foreach ($this->getItemsString() as $item) {
                echo $item;
                @ob_flush();
                flush();
            }
        });
    }

    #[Route(path: '/json')]
    public function streamedJsonResponse(): StreamedJsonResponse
    {
        return new StreamedJsonResponse($this->getItems());
    }

    #[Route(path: '/file')]
    public function binaryFileResponse(): BinaryFileResponse
    {
        $file = $this->cacheDir.'/file.txt';

        if (!file_exists($file)) {
            $fp = fopen($file, 'w') ?: throw new \LogicException('Can not create file for write.');

            foreach ($this->getItemsString() as $item) {
                fwrite($fp, $item);
            }

            fclose($fp);
        }

        return new BinaryFileResponse(
            file: $file,
            headers: [
                'Content-Type' => 'text/plain;charset=UTF-8',
                'Content-Disposition' => 'attachment',
            ],
        );
    }

    private function getItems(): \Generator
    {
        for ($i = 0; $i < 1_000_000; ++$i) {
            yield new readonly class($i + 1) {
                public string $title;
                public string $description;
                public bool $enabled;

                public function __construct(public int $id)
                {
                    $this->title = 'Title';
                    $this->description = 'Description';
                    $this->enabled = true;
                }
            };
        }
    }

    private function getItemsString(): \Generator
    {
        foreach ($this->getItems() as $item) {
            yield \sprintf(
                'id: %s; title: %s; description: %s; enabled: %s'.PHP_EOL,
                $item->id,
                $item->title,
                $item->description,
                $item->enabled ? 'true' : 'false',
            );
        }
    }
}
