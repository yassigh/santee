<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class RegistrationController extends AbstractController
{
    private string $jwtSecret;

    public function __construct()
    {
        // Récupérer la clé secrète depuis le fichier .env
        $this->jwtSecret = $_ENV['JWT_SECRET_KEY'] ?? 'default_secret_key'; // À remplacer par une clé par défaut pour éviter les erreurs
    }

    /**
     * Endpoint pour l'inscription
     */
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (!isset($data['email'], $data['nom'], $data['prenom'], $data['password'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        // Vérification de l'existence de l'utilisateur
        if ($entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already registered'], 400);
        }

        // Création d'un nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], 201);
    }

    /**
     * Endpoint pour la connexion
     */
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        // Recherche de l'utilisateur
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        // Génération du token JWT
        $payload = [
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
          
            'exp' => time() + 3600, // Expiration (1 heure)
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return new JsonResponse(['token' => $jwt], 200);
    }
}
