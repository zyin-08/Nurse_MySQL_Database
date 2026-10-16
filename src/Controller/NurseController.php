<?php

namespace App\Controller;

use App\Entity\Nurse;
use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NurseController extends AbstractController
{
    public function __construct(private NurseRepository $nurseRepository)
    {
    }

    #[Route('/nurse/name/{name}', name: 'nurse_find_by_name', methods: ['GET'])]
    public function findByName(string $name): JsonResponse
    {
        // The utf8mb4_unicode_ci collation makes this comparison case-insensitive
        $nurse = $this->nurseRepository->findOneBy(['name' => $name]);

        if (!$nurse) {
            return $this->json(['error' => 'Nurse not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->toArray($nurse));
    }

    #[Route('/nurse/index', name: 'nurse_index', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $nurses = $this->nurseRepository->findAll();

        return $this->json(array_map($this->toArray(...), $nurses));
    }

    #[Route('/nurse/login', name: 'nurse_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return $this->json(['success' => false, 'message' => 'Username and password are required'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($this->nurseRepository->findByEmailOrName($username) as $nurse) {
            if (password_verify($password, $nurse->getPassword())) {
                return $this->json(['success' => true]);
            }
        }

        return $this->json(['success' => false], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * The password hash is never exposed in API responses.
     */
    private function toArray(Nurse $nurse): array
    {
        return [
            'id' => $nurse->getId(),
            'name' => $nurse->getName(),
            'email' => $nurse->getEmail(),
        ];
    }
}
