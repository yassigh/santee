<?php
namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UserRepository; 
class ActivityController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;

    // Injection de l'interface JWTTokenManagerInterface
    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
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
    public function add(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        try {
            // Récupérer et valider les données de l'activité
            $data = json_decode($request->getContent(), true);
            
            if (empty($data['activity']) || empty($data['heure']) || empty($data['user_id'])) {
                return $this->json(['error' => 'Missing required fields: activity, heure, or user_id'], Response::HTTP_BAD_REQUEST);
            }
    
            // Trouver l'utilisateur en fonction de l'ID
            $user = $userRepository->find($data['user_id']);
            if (!$user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
    
            // Créer et sauvegarder l'activité
            $activity = new Activity();
            $activity->setNom($data['activity']);
            $activity->setHeure($data['heure']);
            if (!empty($data['age'])) {
                $activity->setAge((int)$data['age']);
            }
    
            // Associer l'utilisateur à l'activité
            $activity->setUser($user);
    
            // Persister et enregistrer l'entité
            $em->persist($activity);
            $em->flush();
    
            return $this->json(['message' => 'Activity saved successfully'], Response::HTTP_CREATED);
    
        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
        
    
}
