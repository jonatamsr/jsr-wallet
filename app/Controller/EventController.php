<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AccountNotFoundException;
use App\Service\AccountService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class EventController extends AbstractController
{
    public function __construct(
        private readonly AccountService $service,
    ) {}

    #[PostMapping(path: '/event')]
    public function event(): ResponseInterface
    {
        $type = (string) $this->request->input('type', '');
        $amount = (int) $this->request->input('amount', 0);

        return match ($type) {
            'deposit' => $this->handleDeposit($amount),
            'withdraw' => $this->handleWithdraw($amount),
            'transfer' => $this->handleTransfer($amount),
            default => $this->response->raw('0')->withStatus(400),
        };
    }

    private function handleDeposit(int $amount): ResponseInterface
    {
        $destination = (string) $this->request->input('destination', '');
        $account = $this->service->deposit($destination, $amount);

        return $this->response->json([
            'destination' => $account->toArray(),
        ])->withStatus(201);
    }

    private function handleWithdraw(int $amount): ResponseInterface
    {
        $origin = (string) $this->request->input('origin', '');

        try {
            $account = $this->service->withdraw($origin, $amount);

            return $this->response->json([
                'origin' => $account->toArray(),
            ])->withStatus(201);
        } catch (AccountNotFoundException) {
            return $this->response->raw('0')->withStatus(404);
        }
    }

    private function handleTransfer(int $amount): ResponseInterface
    {
        $origin = (string) $this->request->input('origin', '');
        $destination = (string) $this->request->input('destination', '');

        try {
            $result = $this->service->transfer($origin, $destination, $amount);

            return $this->response->json([
                'origin' => $result->getOrigin()->toArray(),
                'destination' => $result->getDestination()->toArray(),
            ])->withStatus(201);
        } catch (AccountNotFoundException) {
            return $this->response->raw('0')->withStatus(404);
        }
    }
}
