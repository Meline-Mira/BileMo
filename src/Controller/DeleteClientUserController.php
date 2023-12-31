<?php

namespace App\Controller;

use App\Entity\ClientUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class DeleteClientUserController extends AbstractController
{
    #[Route('/api/clients/{id}', name: 'delete_client', methods: ['DELETE'])]
    public function index(ClientUser $clientUser, EntityManagerInterface $entityManager, TagAwareCacheInterface $cachePool): JsonResponse
    {
        if ($clientUser->getUser()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à accéder à ce client.");
        }

        $cachePool->invalidateTags(["clientsCache"]);
        $entityManager->remove($clientUser);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
