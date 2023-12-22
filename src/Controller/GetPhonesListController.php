<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class GetPhonesListController extends AbstractController
{
    #[Route('/api/phones', name: 'phones', methods: ['GET'])]
    public function index(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
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
