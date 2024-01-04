<?php

namespace App\Controller;

use App\Entity\ClientUser;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;

class DeleteClientUserController extends AbstractController
{
    /**
     * Cette méthode permet de supprimer un client de l'utilisateur.
     */
    #[Route('/api/clients/{id}', name: 'delete_client', methods: ['DELETE'])]
    #[Route('/api/clients/{id}', name: 'details_client', methods: ['GET'])]
    #[OA\Response(
        response: 204,
        description: "Pas de contenu",
        content: new OA\JsonContent(
            type: 'nullable',
        )
    )]
    #[OA\Response(
        response: 401,
        description: "Erreur de connexion",
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'code', type: 'integer', example: 401),
                new OA\Property(property: 'message', type: 'string'),
            ],
        )
    )]
    #[OA\Response(
        response: 403,
        description: "Absence d'autorisation",
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'code', type: 'integer', example: 403),
                new OA\Property(property: 'message', type: 'string'),
            ],
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Objet non trouvé",
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'code', type: 'integer', example: 404),
                new OA\Property(property: 'message', type: 'string'),
            ],
        )
    )]
    #[OA\Tag(name: 'Clients')]
    #[Security(name: 'Bearer')]
    public function index(
        ClientUser $clientUser,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cachePool
    ): JsonResponse
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
