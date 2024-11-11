import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Activity } from './activity.model';  // Assurez-vous que le modèle existe

@Injectable({
  providedIn: 'root'
})
export class ActivityService {
  private apiUrl = 'http://localhost:8000/activity/add';  // L'URL pour envoyer les données au backend

  constructor(private http: HttpClient) {}

  // Fonction pour ajouter une activité
  addActivity(activity: any): Observable<any> {
    return this.http.post(this.apiUrl, activity, {
      headers: { 'Content-Type': 'application/json' }
    });
  }
  
}
