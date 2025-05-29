import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})

export class UsuariosService {

  constructor(private http: HttpClient) {}

  listarUsuarios() {
    const cuerpo = {
      accion: 'ListarUsuarios'
    };
    return this.http.post(environment.API_USUARIOS, cuerpo);
  }

  modificarUsuario(id: number, nombre: string, email: string, rol: string, activo: boolean) {
    const cuerpo = {
      accion: 'ModificarUsuario',
      id: id,
      nombre: nombre,
      email: email,
      rol: rol,
      activo: activo
    };
    return this.http.post(environment.API_USUARIOS, cuerpo);
  }

  eliminarUsuario(id: number) {
    const cuerpo = {
      accion: 'EliminarUsuario',
      id: id
    };
    return this.http.post(environment.API_USUARIOS, cuerpo);
  }

  obtenerRolUsuario(id: number) {
    const cuerpo = {
      accion: 'ObtenerRolUsuario',
      id: id
    };
    return this.http.post(environment.API_USUARIOS, cuerpo);
  }
}
