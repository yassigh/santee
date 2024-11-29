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
  error: string | null = null;  

  constructor(private fb: FormBuilder, private authService: AuthService, private router: Router ) {}

  ngOnInit(): void {
    // Initialisation du formulaire avec des validations
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],  // Validation de l'email
      password: ['', [Validators.required, Validators.minLength(6)]]  // Validation du mot de passe
    });
  }

  onLogin(): void {
   

    const loginData = this.loginForm.value;

    this.authService.login(loginData).subscribe(
      (response: any) => {
        console.log('Login success', response);
        this.router.navigate(['/categories']);  // Redirect to a dashboard or home page
      },
      (error) => {
        this.errorMessage = 'Invalid credentials';
        console.log('Login failed', error);
      }
    );
  }}
