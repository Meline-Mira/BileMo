<?php

namespace App\Controller\Client;

use App\Entity\ClientUser;
use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetClientUserDetailsController extends AbstractController
{
    /**
     * Cette méthode permet de connaitre les détails d'un client de l'utilisateur.
     */
    #[Route('/api/clients/{id}', name: 'details_client', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne les détails du client',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ClientUser::class, groups: ['getClient']))
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
        description: 'Objet non trouvé',
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
    public function index(ClientUser $clientUser, SerializerInterface $serializer): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var User $clientUserUser */
        $clientUserUser = $clientUser->getUser();

        if ($clientUserUser->getUserIdentifier() !== $user->getUserIdentifier()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à accéder à ce client.");
        }

        $jsonClient = $serializer->serialize($clientUser, 'json', ['groups' => 'getClient']);

        return new JsonResponse($jsonClient, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
