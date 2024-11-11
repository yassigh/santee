import { Component, OnInit } from '@angular/core';
import { ActivityService } from '../activity.service';

import { AuthService } from '../auth.service';

@Component({
  selector: 'app-activity',
  templateUrl: './activity.component.html',
  styleUrls: ['./activity.component.css']
})
export class ActivityComponent implements OnInit {
  age: number = 0;
  selectedActivity: string = '';
  time: number = 0;
  activities: any[] = [];
  goalReached: boolean = false;
  currentDate: Date = new Date();
  userId: number | null = null;
  constructor(private activityService: ActivityService, private authService: AuthService) {}
  

  ngOnInit(): void {
    this.loadActivities();  // Charger les activités au démarrage
    this.getUserId(); // Récupérez l'ID de l'utilisateur connecté
    }
    getUserId(): void {
      // Récupérer l'ID de l'utilisateur connecté via le service d'authentification
      const user = this.authService.getUser();  // Supposons que `getUser()` retourne l'objet utilisateur
      this.userId = user ? user.id : null;
    }
  loadActivities(): void {
    // Simuler des activités ou récupérer via l'API
    this.activities = [
      { nom: 'Lecture' },
      { nom: 'Exercice' },
      { nom: 'Cuisine' }
    ];
  }

  submitActivity(): void {
    // Vérifier si l'objectif est atteint
    if (this.time >= 2) {
      this.goalReached = true;
    }
  
    // Créer l'objet avec les bonnes propriétés
    const newActivity = {
      activity: this.selectedActivity,  // Assurez-vous que 'selectedActivity' contient le nom de l'activité
      heure: this.time,                 // Ou format correct pour 'heure' (peut-être un 'Date' ou autre format)
      age: this.age ,                    // Assurez-vous que 'age' est bien un nombre
      userId: this.userId 
    };
  
    // Appeler le service pour ajouter l'activité (en envoyant les données correctes)
    this.activityService.addActivity(newActivity).subscribe(response => {
      console.log('Activité ajoutée', response);
    }, error => {
      console.error('Erreur lors de l\'ajout de l\'activité', error);
    });
  }
  
}
