<?php

namespace App\Controller;

use App\Entity\ClientUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;

class AddClientUserController extends AbstractController
{
    /**
     * Cette méthode permet d'ajouter un client à l'utilisateur.
     */
    #[Route('/api/clients', name: 'add_client', methods: ['POST'])]
    #[Route('/api/clients/{id}', name: 'details_client', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Retourne les détails du client",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ClientUser::class, groups: ['getClient']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Requête invalide",
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'type', type: 'string', example: 'https://symfony.com/errors/validation'),
                new OA\Property(property: 'title', type: 'string', example: 'Validation Failed'),
                new OA\Property(property: 'detail', type: 'string'),
                new OA\Property(property: 'violations', type: 'array', items: new OA\Items(type: 'object', properties: [
                    new OA\Property(property: 'propertyPath', type: 'string'),
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'template', type: 'string'),
                    new OA\Property(property: 'parameters', type: 'object'),
                    new OA\Property(property: 'type', type: 'string'),
                ])),
            ],
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
    #[OA\RequestBody(
        description: "Créer un nouvel utilisateur",
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
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse
    {
        $client = $serializer->deserialize($request->getContent(), ClientUser::class, 'json');
        $client->setUser($this->getUser());

        $errors = $validator->validate($client);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $cachePool->invalidateTags(["clientsCache"]);
        $entityManager->persist($client);
        $entityManager->flush();

        $jsonClient = $serializer->serialize($client, 'json', ['groups' => 'getClient']);

        $location = $urlGenerator->generate('details_client', ['id' => $client->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonClient, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
