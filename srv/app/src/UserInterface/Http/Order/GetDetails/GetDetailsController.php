<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Order\GetDetails;

use App\Application\Query\OrderDetails\GetDetails\GetDetails;
use App\Application\Query\QueryBus;
use App\Domain\Read\OrderDetails\OrderDetails;
use App\UserInterface\Http\Order\GetDetails\Output\OrderDetailsOutput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class GetDetailsController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    #[Route(path: '/api/orders/{id}', name: 'get_order_details')]
    public function details(Uuid $id): JsonResponse
    {
        $details = $this->queryBus->ask(new GetDetails($id));
        if (!$details instanceof OrderDetails) {
            throw $this->createNotFoundException();
        }

        $dto = OrderDetailsOutput::fromDomain($details);

        return new JsonResponse($dto->normalize(), Response::HTTP_OK);
    }
}
