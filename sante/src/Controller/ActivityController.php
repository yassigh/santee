<?php

namespace App\Controller;

use App\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class ActivityController extends AbstractController
{
    // private $security;

    // public function __construct(Security $security)
    // {
    //     $this->security = $security;
    // }

    #[Route('/activities', name: 'activity_list')]
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
        // Récupérer le token depuis l'en-tête Authorization
        $token = $request->headers->get('Authorization');
        if (!$token) {
            return $this->json(['error' => 'Token missing'], Response::HTTP_UNAUTHORIZED);
        }
// Vérifie que le token commence par Bearer , sinon retourne une erreur.
        if (strpos($token, 'Bearer ') === false) {
            return $this->json(['error' => 'Invalid token format'], Response::HTTP_UNAUTHORIZED);
        }
//Supprime le préfixe Bearer du token pour obtenir le token pur.
        $token = str_replace('Bearer ', '', $token);

        // Validation du token (vous devez ajouter un service pour valider le JWT)
        try {
            // Exemple d'extraction du user à partir du token, selon la bibliothèque que vous utilisez
            $user = $this->security->getUser(); // Cela vous donne l'utilisateur authentifié si le token est valide
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user || !$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Récupération des données de la requête
        $data = json_decode($request->getContent(), true);
        
        // Vérification de la présence des données nécessaires
        if (empty($data['activity']) || empty($data['heure'])) {
            return $this->json(['error' => 'Missing required fields: activity or heure'], Response::HTTP_BAD_REQUEST);
        }

        $nom = $data['activity'];
        $heure = $data['heure'];
        $age = isset($data['age']) ? (int)$data['age'] : null;

        // Validation de l'âge
        if ($age !== null && $age <= 0) {
            return $this->json(['error' => 'Invalid age'], Response::HTTP_BAD_REQUEST);
        }

        // Créer une nouvelle activité et l'associer à l'utilisateur
        $activity = new Activity();
        $activity->setNom($nom);
        $activity->setHeure($heure);
        
        if ($age !== null) {
            $activity->setAge($age); // Assigner l'âge à l'activité
        }

        $activity->setUser($user); // Associer l'utilisateur à l'activité

        // Enregistrer l'activité dans la base de données
        $em->persist($activity);
        $em->flush();

        return $this->json(['message' => 'Activité enregistrée avec succès']);
    }
}
