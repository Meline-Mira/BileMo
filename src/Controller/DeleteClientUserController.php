<?php

namespace App\Controller;

use App\Entity\ClientUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteClientUserController extends AbstractController
{
    #[Route('/api/clients/{id}', name: 'delete_client', methods: ['DELETE'])]
    public function index(ClientUser $clientUser, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($clientUser);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
