<?php 
namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends AbstractController
{
    // Générer un JWT
    private function generateJwt(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // Encode header et payload en base64
        $headerEncoded = base64_encode(json_encode($header));
        $payloadEncoded = base64_encode(json_encode($payload));

        // Créer la signature
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $_ENV['JWT_SECRET_KEY'], true);
        $signatureEncoded = base64_encode($signature);

        // Retourner le JWT assemblé
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    // Décoder le JWT et valider la signature
    private function decodeJwt(string $token): array
    {
        // Séparer le token en trois parties : header, payload, signature
        [$headerEncoded, $payloadEncoded, $signatureEncoded] = explode('.', $token);

        // Décoder le header et le payload
        $header = json_decode(base64_decode($headerEncoded), true);
        $payload = json_decode(base64_decode($payloadEncoded), true);

        // Vérifier la signature
        $signatureExpected = base64_encode(hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $_ENV['JWT_SECRET_KEY'], true));

        if ($signatureEncoded !== $signatureExpected) {
            throw new \Exception('Invalid token signature');
        }

        return $payload;
    }

    #[Route('/activities', name: 'activity_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        $activities = $em->getRepository(Activity::class)->findAll();
        $activityNames = array_map(fn($activity) => [
            'id' => $activity->getId(),
            'nom' => $activity->getNom()
        ], $activities);

        return $this->json($activityNames);
    }

    #[Route('/activity/add', name: 'activity_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        try {
            // Vérifier l'existence du token dans l'en-tête
            $authHeader = $request->headers->get('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return $this->json(['error' => 'Token missing or invalid'], Response::HTTP_UNAUTHORIZED);
            }

            $token = str_replace('Bearer ', '', $authHeader);
            $decoded = $this->decodeJwt($token); // Décoder et vérifier le JWT

            $userId = $decoded['sub']; // Extraire l'ID de l'utilisateur depuis le payload du token

            // Récupérer l'utilisateur depuis la base de données
            $user = $em->getRepository(User::class)->find($userId);
            if (!$user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
            }

            // Récupérer et valider les données de l'activité
            $data = json_decode($request->getContent(), true);
            if (empty($data['activity']) || empty($data['heure'])) {
                return $this->json(['error' => 'Missing required fields: activity or heure'], Response::HTTP_BAD_REQUEST);
            }

            // Créer et enregistrer l'activité
            $activity = new Activity();
            $activity->setNom($data['activity']);
            $activity->setHeure($data['heure']);
            if (!empty($data['age'])) {
                $activity->setAge((int)$data['age']);
            }
            $activity->setUser($user);
            $em->persist($activity);
            $em->flush();

            return $this->json(['message' => 'Activity saved successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
