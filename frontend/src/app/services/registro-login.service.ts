import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class RegistroLoginService {

  constructor(private http: HttpClient) { }

  registrar(nombre: string, email: string, password: string, isParticipante: boolean) {
    const cuerpo = {
      accion: 'RegistrarUsuario',
      nombre: nombre,
      email: email,
      password: password,
      isParticipante: isParticipante
    };
    return this.http.post(environment.API_REGISTRO, cuerpo);
  }

  login(email: string, password: string) {
    const cuerpo = {
      accion: 'LoginUsuario',
      email: email,
      password: password
    };
    return this.http.post(environment.API_LOGIN, cuerpo);
  }

}
