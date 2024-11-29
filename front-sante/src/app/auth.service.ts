
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { catchError, Observable, tap, throwError } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api'; // Remplacez par votre URL d'API
  private token: string | null = null;

  constructor(private http: HttpClient, private router: Router) {}

  saveToken(token: string): void {
    this.token = token;
    localStorage.setItem('authToken', token);  // Stocker le token dans localStorage
    console.log('Token saved:', token);  // Log du token pour vérification
  }

  getToken(): string | null {
    return localStorage.getItem('authToken');  // Récupérer le token depuis localStorage
  }

  login(data: any): Observable<any> {
    console.log('Attempting login with:', data);
    return this.http.post(`${this.apiUrl}/login`, data).pipe(
      tap((response: any) => {
        console.log('Login successful, token:', response.token);
        this.saveToken(response.token);  // Sauvegarder le token si authentification réussie
        this.router.navigate(['/categories']);  // Rediriger vers la page /categories après connexion
      }),
      catchError((error) => {
        console.error('Login failed:', error);
        return throwError(error);
      })
    );
  }

  logout(): void {
    localStorage.removeItem('authToken'); // Supprimer le token lors de la déconnexion
    this.router.navigate(['/login']); // Rediriger vers la page de login
  }

  isAuthenticated(): boolean {
    const token = this.getToken();
    if (!token) {
      return false; // Pas de token, utilisateur non authentifié
    }

    // Vérification de l'expiration du token JWT
    const payload = JSON.parse(atob(token.split('.')[1])); // Décodage du payload
    const exp = payload.exp; // Heure d'expiration en secondes
    const now = Math.floor(Date.now() / 1000); // Heure actuelle en secondes

    if (exp < now) {
      localStorage.removeItem('authToken');  // Supprimer le token expiré
      return false;  // Token expiré, utilisateur non authentifié
    }

    return true;  // Si le token est valide, utilisateur authentifié
  }

  getUser(): any {
    const user = JSON.parse(localStorage.getItem('currentUser') || '{}');
    console.log('Utilisateur dans getUser:', user);  
    return user;
  }
}
