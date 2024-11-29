
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

  constructor(private http: HttpClient,  private router: Router) {}
  saveToken(token: string): void {
    this.token = token;
    localStorage.setItem('authToken', token);  // Store token in localStorage
    console.log('Token saved:', token);  // Log the token to check
  }
  
  

  getToken(): string | null {
    return localStorage.getItem('authToken');  // Retrieve token from localStorage
  }
  
  

  login(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, data).pipe(
      tap((response: any) => {
        console.log('Login successful, token:', response.token);  // Ensure the token is in the response
        this.saveToken(response.token);  // Save the token after login
      })
    );
  }
  
  handleGoogleLogin(token: string): void {
    const headers = { 'Content-Type': 'application/json' };
    const body = { id_token: token };
  
    this.http.post(`${this.apiUrl}/api/google-login`, body, { headers }).subscribe(
      (response: any) => {
        console.log('Google login successful, token:', response.token);  // Ensure the token is in the response
        this.saveToken(response.token);  // Save the token after Google login
        this.router.navigate(['/categories']);
      },
      error => {
        console.error('Erreur lors de la connexion Google:', error);
      }
    );
  }
  
  

  logout(): void {
    localStorage.removeItem('authToken'); // Supprimer le token lors de la déconnexion
    localStorage.removeItem('user'); // Supprimer les détails de l'utilisateur également
    this.router.navigate(['/login']);}

    isAuthenticated(): boolean {
      const token = this.getToken();
      if (!token) {
        return false; // Pas de token, l'utilisateur n'est pas authentifié
      }
   // Vérification de l'expiration du token (si vous utilisez JWT)
   const payload = JSON.parse(atob(token.split('.')[1])); // Décodage du payload
   const exp = payload.exp; // Heure d'expiration du token en secondes
   const now = Math.floor(Date.now() / 1000); // Heure actuelle en secondes
 
   if (exp < now) {
     localStorage.removeItem('authToken');
     return false; // Token expiré, l'utilisateur n'est plus authentifié
   }
 
   return true; // Si le token est valide, l'utilisateur est authentifié
 }
    

    getUser(): any {
      const user = JSON.parse(localStorage.getItem('currentUser') || '{}');
      console.log('Utilisateur dans getUser:', user);  
      return user;
    }

    
}