
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api'; // Remplacez par votre URL d'API

  constructor(private http: HttpClient) {}

  login(email: string, password: string): Observable<any> {
    const headers = new HttpHeaders({ 'Content-Type': 'application/json' });
    return this.http.post(`${this.apiUrl}/login`, { email, password }, { headers })
      
  }

  logout(): void {
    localStorage.removeItem('token'); // Supprimer le token lors de la déconnexion
  }

  isAuthenticated(): boolean {
    return !!localStorage.getItem('token'); // Vérifier la présence du token
  }

  private currentUser = {
    id: 1,           // Exemple d'ID d'utilisateur
    nom: 'yassine gh'  // Autres propriétés de l'utilisateur, si nécessaire
  };

  getUser() {
    // Retourne l'utilisateur connecté
    return this.currentUser;
  }
}