<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordHasherInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/api')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
public function register(Request $request,EntityManagerInterface $entityManager
): JsonResponse {
    $data = json_decode($request->getContent(), true);
    
    if (!$data) {
        return new JsonResponse(['error' => 'Invalid JSON or no data received'], 400);
    }
    
    if (!isset($data['email'], $data['nom'], $data['prenom'], $data['password'])) {
        return new JsonResponse(['error' => 'Missing required fields'], 400);
    }

    try {
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setPassword($data['password']); // Stocke le mot de passe en clair

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], 201);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}

    
    
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): JsonResponse
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $response = [
            'lastUsername' => $lastUsername,
            'error' => $error ? $error->getMessage() : null,
        ];

        return new JsonResponse($response, JsonResponse::HTTP_OK);
    }
     /**
     * @Route("/api/auth/google", name="api_auth_google", methods={"POST"})
     */
    public function googleAuth(Request $request): JsonResponse
    {
        $token = $request->get('token');
        $googleKeysUrl = 'https://www.googleapis.com/oauth2/v3/certs';

        try {
            $decodedToken = JWT::decode($token, JWK::parseKeySet(file_get_contents($googleKeysUrl)), ['RS256']);
            $email = $decodedToken->email;

            // Effectuer la connexion ou l'inscription de l'utilisateur ici
            // Vous pouvez récupérer les informations utilisateur et les stocker dans la base de données

            return new JsonResponse(['message' => 'Authentification réussie'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Authentification échouée'], 401);
        }
    }
}
