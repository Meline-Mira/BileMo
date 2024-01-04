<?php

namespace App\Controller;

use App\Entity\Phone;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class GetPhoneDetailsController extends AbstractController
{
    /**
     * Cette méthode permet de connaitre les détails d'un téléphone.
     */
    #[Route('/api/phones/{id}', name: 'details_phone', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Retourne les détails du téléphone",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Phone::class, groups: ['getPhone']))
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
    #[OA\Tag(name: 'Phones')]
    #[Security(name: 'Bearer')]
    public function index(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $jsonPhone = $serializer->serialize($phone, 'json', ['groups' => 'getPhone']);
        return new JsonResponse($jsonPhone, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
