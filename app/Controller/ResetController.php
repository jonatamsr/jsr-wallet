<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AccountService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class ResetController extends AbstractController
{
    public function __construct(
        private readonly AccountService $service,
    ) {}

    #[PostMapping(path: '/reset')]
    public function reset(): ResponseInterface
    {
        $this->service->reset();

        return $this->response->raw('OK')->withStatus(200);
    }
}
