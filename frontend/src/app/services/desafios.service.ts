import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.prod';

@Injectable({
  providedIn: 'root'
})
export class DesafiosService {

  constructor(private http: HttpClient) { }

 
  listarDesafio() {
    const cuerpo = {
      accion: 'ListarDesafios'
    };
    return this.http.post(environment.API_DESAFIOS, cuerpo);
  }

  modificarDesafio(id: number, nombre: string, descripcion: string, fecha_inicio: string, fecha_fin: string) {
    const cuerpo = {
      accion: 'ModificarDesafio',
      id: id,
      nombre: nombre,
      descripcion: descripcion,
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin
    };
    return this.http.post(environment.API_DESAFIOS, cuerpo);
  }

  eliminarDesafio(id: number) {
    const cuerpo = {
      accion: 'EliminarDesafio',
      id: id
    };
    return this.http.post(environment.API_DESAFIOS, cuerpo);
  }

    subirFoto(nombre: string, descripcion: string,fecha_inicio: number, fecha_fin: number, archivoBase64: string, ) {
    const cuerpo = {
      accion: 'SubirDesafio',
      nombre: nombre,
      descripcion: descripcion,
      archivo: archivoBase64,
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin
    };
    return this.http.post(environment.API_SUBIDADESAFIOS, cuerpo);
  }
}


