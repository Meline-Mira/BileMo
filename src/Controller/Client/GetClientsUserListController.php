<?php

namespace App\Controller\Client;

use App\Entity\ClientUser;
use App\Entity\User;
use App\Repository\ClientUserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class GetClientsUserListController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des clients d'un utilisateur.
     */
    #[Route('/api/clients', name: 'clients', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Retourne la liste des clients de l'utilisateur",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ClientUser::class, groups: ['getClients']))
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Erreur de connexion',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'code', type: 'integer', example: 401),
                new OA\Property(property: 'message', type: 'string'),
            ],
        )
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: "La page que l'on veut récupérer",
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: "Le nombre d'éléments que l'on veut récupérer",
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Tag(name: 'Clients')]
    #[Security(name: 'Bearer')]
    public function index(
        ClientUserRepository $clientUserRepository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /** @var int $page */
        $page = $request->get('page', 1);
        /** @var int $limit */
        $limit = $request->get('limit', 5);

        $idCache = 'getAllClients-'.$page.'-'.$limit;

        $jsonClientsList = $cachePool->get($idCache, function (ItemInterface $item) use ($clientUserRepository, $page, $limit, $serializer) {
            $item->tag('clientsCache');

            /** @var User $user */
            $user = $this->getUser();

            $clientsList = $clientUserRepository->findAllWithPagination($user, $page, $limit);

            return $serializer->serialize($clientsList, 'json', ['groups' => 'getClients']);
        });

        return new JsonResponse($jsonClientsList, Response::HTTP_OK, [], true);
    }
}
