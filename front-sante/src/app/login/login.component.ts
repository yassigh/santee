import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { gapi } from 'gapi-script';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  private clientId: string = 'VOTRE_CLIENT_ID_GOOGLE';
  loginForm!: FormGroup;
  error: string | null = null; 

  constructor(private fb: FormBuilder, private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    // this.initGoogleAuth();

  
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
   password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }


  onSubmit(): void {
    if (this.loginForm.valid) { 
       this.router.navigate(['/categories']);

      console.log('Form Submitted', this.loginForm.value);
    } else {
      this.error = 'Please fill out the form correctly.';
    }
  }

  // private initGoogleAuth(): void {
  //   gapi.load('auth2', () => {
  //     gapi.auth2.init({
  //       client_id: this.clientId,
  //       cookie_policy: 'single_host_origin', 
  //       scope: 'profile email'
  //     });
  //   });
  // }

  // onGoogleSignIn() {
  //   const auth2 = gapi.auth2.getAuthInstance();
  //   auth2.signIn().then((googleUser: any) => {
  //     const profile = googleUser.getBasicProfile();
  //     const token = googleUser.getAuthResponse().id_token;
  //     console.log('Token ID:', token);
  //     this.sendTokenToBackend(token);
  //   });
  // }

  // private sendTokenToBackend(token: string) {
  //   this.http.post('http://localhost:8000/api/auth/google', { token }).subscribe(response => {
  //       console.log('Authentification réussie:', response);
  //     }, error => {
  //       this.error = 'Erreur d\'authentification: ' + error.message;
  //     });
  // }
}
