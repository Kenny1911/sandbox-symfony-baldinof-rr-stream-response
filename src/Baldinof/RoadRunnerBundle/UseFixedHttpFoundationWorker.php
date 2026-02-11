<?php

declare(strict_types=1);

namespace App\Baldinof\RoadRunnerBundle;

use Baldinof\RoadRunnerBundle\RoadRunnerBridge\HttpFoundationWorkerInterface;
use Spiral\RoadRunner\WorkerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UseFixedHttpFoundationWorker implements HttpFoundationWorkerInterface
{
    private HttpFoundationWorkerInterface $inner;

    public function __construct(
        HttpFoundationWorkerInterface $origWorker,
        HttpFoundationWorkerInterface $fixedWorker,
        bool $useFixed,
    ) {
        $this->inner = $useFixed ? $fixedWorker : $origWorker;
    }

    public function waitRequest(): ?Request
    {
        return $this->inner->waitRequest();
    }

    public function respond(Response $response): void
    {
        $this->inner->respond($response);
    }

    public function getWorker(): WorkerInterface
    {
        return $this->inner->getWorker();
    }
}
