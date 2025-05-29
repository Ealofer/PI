export interface Usuario {
  id_usuario: number;
  nombre: string;
  email: string;
  password: string; 
  fecha_creacion: string;
  rol: 'Administrador' | 'Participante' | 'Usuario';
  activo: boolean;
}
