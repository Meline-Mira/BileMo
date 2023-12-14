<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetPhonesListController extends AbstractController
{
    #[Route('/api/phones', name: 'phones', methods: ['GET'])]
    public function index(PhoneRepository $phoneRepository, SerializerInterface $serializer): JsonResponse
    {
        $phonesList = $phoneRepository->findAll();
        $jsonPhonesList = $serializer->serialize($phonesList, 'json', ['groups' => 'getPhones']);

        return new JsonResponse($jsonPhonesList, Response::HTTP_OK, [], true);
    }
}
