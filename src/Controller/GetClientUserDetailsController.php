<?php

namespace App\Controller;

use App\Entity\ClientUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetClientUserDetailsController extends AbstractController
{
    #[Route('/api/clients/{id}', name: 'details_client', methods: ['GET'])]
    public function index(ClientUser $clientUser, SerializerInterface $serializer): JsonResponse
    {
        if ($clientUser->getUser()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à accéder à ce client.");
        }

        $jsonClient = $serializer->serialize($clientUser, 'json', ['groups' => 'getClient']);
        return new JsonResponse($jsonClient, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
