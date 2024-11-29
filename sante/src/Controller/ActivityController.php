<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends AbstractController
{
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
   { try {
        // Vérification du token et récupération de l'utilisateur
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json(['error' => 'Token missing or invalid'], Response::HTTP_UNAUTHORIZED);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = JWT::decode($token, 'your_secret_key', ['HS256']);
        $userId = $decoded->sub;

        // Récupérer l'utilisateur
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
        // Log l'erreur pour obtenir plus d'informations
        return $this->json(['error' => 'Internal server error', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}}
