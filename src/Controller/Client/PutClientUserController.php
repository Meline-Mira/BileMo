<?php

namespace App\Controller\Client;

use App\Entity\ClientUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class PutClientUserController extends AbstractController
{
    /**
     * Cette méthode permet de modifier les informations d'un client à l'utilisateur.
     */
    #[Route('/api/clients/{id}', name: 'put_client', methods: ['PUT'])]
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
    #[OA\RequestBody(
        description: 'Modifier un utilisateur',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                new OA\Property(property: 'lastName', type: 'string', example: 'Doe'),
                new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com'),
            ],
        )
    )]
    #[OA\Tag(name: 'Clients')]
    #[Security(name: 'Bearer')]
    public function index(
        ClientUser $clientUser,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        /** @var User $clientUserUser */
        $clientUserUser = $clientUser->getUser();

        if ($clientUserUser->getUserIdentifier() !== $user->getUserIdentifier()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à accéder à ce client.");
        }

        $client = $serializer->deserialize($request->getContent(), ClientUser::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $clientUser]);
        $client->setUser($user);

        $errors = $validator->validate($client);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $cachePool->invalidateTags(['clientsCache']);

        $entityManager->persist($client);
        $entityManager->flush();

        $jsonClient = $serializer->serialize($client, 'json', ['groups' => 'getClient']);

        $location = $urlGenerator->generate('details_client', ['id' => $client->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonClient, Response::HTTP_CREATED, ['Location' => $location], true);
    }
}
