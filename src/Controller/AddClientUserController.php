<?php

namespace App\Controller;

use App\Entity\ClientUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AddClientUserController extends AbstractController
{
    #[Route('/api/clients', name: 'add_client', methods: ['POST'])]
    public function index(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager,
    UrlGeneratorInterface $urlGenerator, UserRepository $userRepository): JsonResponse
    {
        $client = $serializer->deserialize($request->getContent(), ClientUser::class, 'json');
        $content = $request->toArray();
        $userId = $content['user'];
        $client->setUser($userRepository->find($userId));

        $entityManager->persist($client);
        $entityManager->flush();

        $jsonClient = $serializer->serialize($client, 'json', ['groups' => 'getClient']);

        $location = $urlGenerator->generate('details_client', ['id' => $client->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonClient, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
