import { Component, OnInit } from '@angular/core';
import { ActivityService } from '../activity.service';
import { AuthService } from '../auth.service';
import { HttpHeaders } from '@angular/common/http';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, throwError } from 'rxjs';

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
  selectedPeriod: string = 'jour';

  private apiUrl = 'http://localhost:8000/api';
  constructor(
    private activityService: ActivityService,
    private authService: AuthService,
    private http: HttpClient,private router:Router
  ) {}

  ngOnInit(): void {
    const token = this.authService.getToken();
    if (!token) {
      this.router.navigate(['/login']);  // Redirect to login if token is missing
    } else {
      this.loadActivities();  // Load activities if token is available
      this.getUserId();  // Get user ID
    }
  }
  

  getUserId(): void {
    const user = this.authService.getUser();  // Supposons que `getUser()` retourne l'objet utilisateur
    this.userId = user ? user.id : null;
  }

  loadActivities(): void {
    this.activities = [
      { nom: 'Lecture' },
      { nom: 'Exercice' },
      { nom: 'Cuisine' }
    ];
  }

  submitActivity(): void {
    if (this.time >= 2) {
      this.goalReached = true;
    }
   
    const newActivity = {
      activity: this.selectedActivity,
      heure: this.time,
      age: this.age,
      userId: this.userId  // Assurez-vous que l'ID de l'utilisateur est présent
    };

    // Appeler le service pour ajouter l'activité
    this.addActivity(newActivity);
  }

  addActivity(activity: any): Observable<any> {
    const token = this.authService.getToken();
    if (!token) {
      return throwError('Token is missing');
    }
  
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);
    return this.http.post(`${this.apiUrl}/activity/add`, activity, { headers });
  }
  
  
  
}
