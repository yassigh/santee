import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { Activity } from './activity.model';  // Assurez-vous que le modèle existe

@Injectable({
  providedIn: 'root'
})
export class ActivityService {
  private apiUrl = 'http://localhost:8000/activity/add';  // L'URL pour envoyer les données au backend

  constructor(private http: HttpClient) {}

  addActivity(activity: any): Observable<any> {
    const token = localStorage.getItem('authToken');
    if (!token) {
      console.error('User not authenticated');
      return throwError('User not authenticated'); // Retourner une erreur si le token n'est pas présent
    }
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`, // Ajouter le jeton d'autorisation
      'Content-Type': 'application/json'
    });
    return this.http.post(this.apiUrl, activity, { headers });
  }
  
}
