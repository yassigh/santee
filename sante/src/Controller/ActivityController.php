<?php

namespace App\Controller;

use App\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ActivityController extends AbstractController
{
    #[Route('/activities', name: 'activity_list')]
    public function list(EntityManagerInterface $em): Response
    {
        // Récupérer toutes les activités de la base de données
        $activities = $em->getRepository(Activity::class)->findAll();

        // Transformer les activités en format JSON pour l'API
        $activityNames = array_map(fn($activity) => [
            'id' => $activity->getId(),
            'nom' => $activity->getNom()
        ], $activities);

        return $this->json($activityNames);
    }

    #[Route('/activity/add', name: 'activity_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);  // Décoder le JSON
        dump($data);  // Pour déboguer, vérifiez les données envoyées
    
        $nom = $data['activity'] ?? null;  // Récupérer le nom de l'activité
        $heure = $data['heure'] ?? null;
        $age = $data['age'] ?? null;
    
        if (!$nom) {
            return $this->json(['error' => 'Nom is required'], Response::HTTP_BAD_REQUEST);
        }
    
        $activity = new Activity();
        $activity->setNom($nom);
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }
        if ($heure !== null) {
            $activity->setHeure($heure);  // Enregistrer directement l'heure comme nombre
        }
    
        if ($age) {
            $activity->setAge((int)$age);
        }
    
        $em->persist($activity);
        $em->flush();
    
        return $this->json(['message' => 'Activité enregistrée avec succès']);
    }
    
    

}
