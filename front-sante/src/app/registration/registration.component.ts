// src/app/registration/registration.component.ts

import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RegistrationService } from '../registration.service';

@Component({
  selector: 'app-registration',
  templateUrl: './registration.component.html',
  styleUrls: ['./registration.component.css']
})
export class RegistrationComponent {
  registrationForm: FormGroup;

  constructor(private fb: FormBuilder, private registrationService: RegistrationService) {
    this.registrationForm = this.fb.group({
      nom: ['', Validators.required],
      prenom: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onSubmit(): void {
    if (this.registrationForm.valid) {
      this.registrationService.register(this.registrationForm.value).subscribe({
        next: (response) => {
          console.log('User registered successfully', response);
          // Vous pouvez ajouter une redirection ou un message de succès ici
        },
        error: (error) => {
          console.error('Registration error', error);
          // Gérez l'erreur ici (afficher un message, etc.)
        }
      });
    }
  }
}
