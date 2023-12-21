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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddClientUserController extends AbstractController
{
    #[Route('/api/clients', name: 'add_client', methods: ['POST'])]
    public function index(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager,
    UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, ValidatorInterface $validator): JsonResponse
    {
        $client = $serializer->deserialize($request->getContent(), ClientUser::class, 'json');
        $client->setUser($this->getUser());

        $errors = $validator->validate($client);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($client);
        $entityManager->flush();

        $jsonClient = $serializer->serialize($client, 'json', ['groups' => 'getClient']);

        $location = $urlGenerator->generate('details_client', ['id' => $client->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonClient, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
