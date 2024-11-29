import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service'; // Assurez-vous que le chemin est correct

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  errorMessage: string = '';
  loginForm!: FormGroup;
  
  constructor(private fb: FormBuilder, private authService: AuthService, private router: Router) {}

  ngOnInit(): void {
    // Initialisation du formulaire avec des validations
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],  // Validation de l'email
      password: ['', [Validators.required, Validators.minLength(6)]]  // Validation du mot de passe
    });
  }

  onLogin(): void {
    const loginData = this.loginForm.value;
    console.log('Login Data:', loginData);  // Vérifiez que les données sont correctes

    this.authService.login(loginData).subscribe(
      (response: any) => {
        console.log('Login success', response);
        // Assurez-vous que le token est bien sauvegardé
        this.authService.saveToken(response.token);
        this.router.navigate(['/categories']);  // Redirige vers la page de catégories
      },
      (error) => {
        this.errorMessage = 'Invalid credentials';
        console.log('Login failed', error);
      }
    );
  }
  }
