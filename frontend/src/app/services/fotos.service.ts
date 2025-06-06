import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.prod';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class FotosService {

  constructor(private http: HttpClient) { }

  listarFotos(id: number) {
    const cuerpo = {
      accion: 'ListarFotos',
      id: id
    };
    return this.http.post(environment.API_FOTOS, cuerpo);
  }

  cambiarEstadoFoto(id_foto: number, estado: string) {
    const cuerpo = {
      accion: 'CambiarEstadoFoto',
      id_foto: id_foto,
      estado: estado
    };
    return this.http.post(environment.API_FOTOS, cuerpo);
  }

  eliminarFoto(id_foto: number) {
    const cuerpo = {
      accion: 'EliminarFoto',
      id_foto: id_foto
    };
    return this.http.post(environment.API_FOTOS, cuerpo);
  }

  subirFoto(nombre: string, descripcion: string, archivoBase64: string, id_usuario: number, id_desafio: number) {
    const cuerpo = {
      accion: 'SubirFoto',
      nombre_foto: nombre,
      descripcion: descripcion,
      archivo: archivoBase64,
      id_usuario: id_usuario,
      id_desafio: id_desafio
    };
    return this.http.post(environment.API_SUBIDAFOTOS, cuerpo);
  }

  modificarFoto(id_foto: number, nombre: string, descripcion: string) {
    const cuerpo = {
      accion: 'EditarFoto',
      id_foto: id_foto,
      nombre_foto: nombre,
      descripcion: descripcion
    };
    return this.http.post(environment.API_FOTOS, cuerpo);
  }

}
