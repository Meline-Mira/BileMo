<?php

namespace App\Controller;

use App\Repository\ClientUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetClientsUserListController extends AbstractController
{
    #[Route('/api/clients', name: 'clients', methods: ['GET'])]
    public function index(ClientUserRepository $clientUserRepository, SerializerInterface $serializer): JsonResponse
    {
        $clientsList = $clientUserRepository->findAll();
        $jsonPhonesList = $serializer->serialize($clientsList, 'json', ['groups' => 'getClients']);

        return new JsonResponse($jsonPhonesList, Response::HTTP_OK, [], true);
    }
}
