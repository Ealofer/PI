import { Component } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { RegistroLoginService } from '../../services/registro-login.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-registro-login',
  imports: [ReactiveFormsModule],
  templateUrl: './registro-login.component.html',
  styleUrls: ['./registro-login.component.css']
})
export class RegistroLoginComponent {
  isLoginMode = true;
  loginForm: FormGroup;
  registerForm: FormGroup;
  errorMessage = '';
  successMessage = '';

  constructor(private fb: FormBuilder, private registroLoginService: RegistroLoginService, private ruta: Router) {
    this.loginForm = this.fb.group({
      email: this.fb.control('', [Validators.required, Validators.email]),
      password: this.fb.control('', Validators.required)
    });
    this.registerForm = this.fb.group({
      nombre: this.fb.control('', Validators.required),
      email: this.fb.control('', [Validators.required, Validators.email]),
      password: this.fb.control('', [Validators.required, Validators.minLength(6)]),
      confirmPassword: this.fb.control('', Validators.required),
      isParticipante: this.fb.control(false)
    });
  }

  toggleMode() {
    this.isLoginMode = !this.isLoginMode;
    this.errorMessage="";
  }

  onSubmit() {
  const form = this.isLoginMode ? this.loginForm : this.registerForm;
  console.log("form:", form.value);

  if (this.isLoginMode) {
    this.registroLoginService.login(form.value.email, form.value.password).subscribe({
      next: (data: any) => {
        if (data.status === 'success') {
          console.log(data);
          this.ruta.navigate(['/principal', data.user.id_usuario]);
        } else {
          this.errorMessage = data.message;
        }
      }
    });
  } else {
    //Si las contraseñas no son las mismas error
    if (form.value.password !== form.value.confirmPassword) {
      this.errorMessage="Las contraseñas no coinciden";
      return;
    }

    this.registroLoginService.registrar(form.value.nombre, form.value.email, form.value.password, form.value.isParticipante).subscribe({
      next: (data: any) => {
        if (data.status === 'success') {
          this.toggleMode();
          this.registerForm.reset();
        } else {
          this.errorMessage = data.message;
        }
      }
    });
  }
}


}
