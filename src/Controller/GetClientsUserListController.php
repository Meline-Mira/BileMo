<?php

namespace App\Controller;

use App\Repository\ClientUserRepository;
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
    #[Route('/api/clients', name: 'clients', methods: ['GET'])]
    public function index(ClientUserRepository $clientUserRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 5);

        $idCache = "getAllClients-" . $page . "-" . $limit;

        $jsonClientsList = $cachePool->get($idCache, function (ItemInterface $item) use ($clientUserRepository, $page, $limit, $serializer) {
            $item->tag("clientsCache");
            $clientsList = $clientUserRepository->findAllWithPagination($this->getUser(), $page, $limit);
            return $serializer->serialize($clientsList, 'json', ['groups' => 'getClients']);
        });

        return new JsonResponse($jsonClientsList, Response::HTTP_OK, [], true);
    }
}
