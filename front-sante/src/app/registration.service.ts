// src/app/registration.service.ts

import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RegistrationService {
  private apiUrl = 'http://localhost:8000/api'; // Modifiez l'URL selon votre configuration

  constructor(private http: HttpClient) {}

  register(user: { nom: string; prenom: string; email: string; password: string }): Observable<any> {
    return this.http.post<any>(this.apiUrl, user, {
      headers: new HttpHeaders({ 'Content-Type': 'application/json' }),
    });
  }
}
