<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class GetPhonesListController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des téléphones.
     */
    #[Route('/api/phones', name: 'phones', methods: ['GET'])]
    #[OA\Response(
    response: 200,
    description: "Retourne la liste des téléphones",
    content: new OA\JsonContent(
        type: 'array',
        items: new OA\Items(ref: new Model(type: Phone::class, groups: ['getPhones']))
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
    #[OA\Tag(name: 'Phones')]
    #[Security(name: 'Bearer')]
    public function index(
        PhoneRepository $phoneRepository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cachePool
    ): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 5);

        $idCache = "getAllPhones-" . $page . "-" . $limit;

        $jsonPhonesList = $cachePool->get($idCache, function (ItemInterface $item) use ($phoneRepository, $page, $limit, $serializer) {
            $item->tag("phonesCache");
            $phonesList = $phoneRepository->findAllWithPagination($page, $limit);
            return $serializer->serialize($phonesList, 'json', ['groups' => 'getPhones']);
        });

        return new JsonResponse($jsonPhonesList, Response::HTTP_OK, [], true);
    }
}
