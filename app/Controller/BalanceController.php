<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AccountNotFoundException;
use App\Service\AccountService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class BalanceController extends AbstractController
{
    public function __construct(
        private readonly AccountService $service,
    ) {}

    #[GetMapping(path: '/balance')]
    public function balance(): ResponseInterface
    {
        $accountId = $this->request->query('account_id', '');

        try {
            $balance = $this->service->getBalance($accountId);

            return $this->response->raw((string) $balance)->withStatus(200);
        } catch (AccountNotFoundException) {
            return $this->response->raw('0')->withStatus(404);
        }
    }
}
