<?php

namespace App\Controller;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api/google-login', name: 'api_google_login', methods: ['POST'])]
    public function googleLogin(Request $request): JsonResponse
    {
        // Retrieve the token from the request body
        $data = json_decode($request->getContent(), true);

        if (!isset($data['token'])) {
            return new JsonResponse(['error' => 'Token is missing'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $googleToken = $data['token'];
        $googleKeysUrl = 'https://www.googleapis.com/oauth2/v3/certs';

        try {
            // Fetch Google's public keys and decode the token
            $googleKeys = json_decode(file_get_contents($googleKeysUrl), true);
            $decodedToken = JWT::decode($googleToken, JWK::parseKeySet($googleKeys), ['RS256']);

            // Extract user information from the decoded token
            $email = $decodedToken->email;
            
            // Perform user authentication or registration logic here
            // You can search for the user in the database or create a new user

            return new JsonResponse(['message' => 'Authentification réussie', 'email' => $email], JsonResponse::HTTP_OK);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return new JsonResponse(['error' => 'Token has expired', 'details' => $e->getMessage()], JsonResponse::HTTP_UNAUTHORIZED);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return new JsonResponse(['error' => 'Invalid token signature', 'details' => $e->getMessage()], JsonResponse::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Authentication failed: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
