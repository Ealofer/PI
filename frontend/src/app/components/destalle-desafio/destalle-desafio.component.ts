import { Component } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { FotosService } from '../../services/fotos.service';
import { UsuariosService } from '../../services/usuarios.service';
import { ActivatedRoute, RouterLink } from '@angular/router';

@Component({
  selector: 'app-destalle-desafio',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './destalle-desafio.component.html',
  styleUrl: './destalle-desafio.component.css'
})
export class DestalleDesafioComponent {
fotos: any[] = [];
fotosAprobadas: any[] = [];
fotosPendientes: any[] = [];
rolUsuario: string = '';
id_desafio: number = 0;
id_usuario: number = 0;
showForm: boolean = false;
fotoForm!: FormGroup;
fotoSeleccionado: boolean = false;
fImagen: File | null = null;
inputFile: any = null;
imagen64:string = "";
showFormEditar: boolean = false;
fotoFormEditar: FormGroup;
idFotoEditar: number=0;


constructor(private fb: FormBuilder, private fotosService: FotosService, private usuarioService: UsuariosService, private route: ActivatedRoute) {
  this.fotoForm = this.fb.group({
    nombre_foto: ['', Validators.required],
    descripcion: ['', Validators.required],
  });

  this.fotoFormEditar = this.fb.group({
    nombre_foto: ['', Validators.required],
    descripcion: ['', Validators.required],
  });
}

ngOnInit() {
  const idParam = this.route.snapshot.paramMap.get('id_desafio');
  if(idParam){
    this.id_desafio = parseInt(idParam, 10);
  }
  const idParam2 = this.route.snapshot.paramMap.get('id_usuario');
    if(idParam2){
      this.id_usuario = parseInt(idParam2, 10);
      this.usuarioService.obtenerRolUsuario(this.id_usuario).subscribe({
        next: (res: any) => {
          if (res.status === 'success') {
            this.rolUsuario = res.rol;
          }
        },
      error: (error) => 
        console.error('Error al cargar rol del usuario: ', error)
      });
    }
  this.cargarFotos();
}

toggleForm() {
  this.showForm = !this.showForm;
}

toggleFormEditar(foto: any) {
  this.showFormEditar = !this.showFormEditar;
  this.idFotoEditar=foto.id_foto;
  this.fotoFormEditar.patchValue({
    nombre_foto: foto.nombre_foto,
    descripcion: foto.descripcion
  });
}

cargarFotos() {
  this.fotosService.listarFotos(this.id_desafio).subscribe((
    res: any) => {
    if (res.status === 'success') {
      this.fotos = res.fotos;
      this.fotosAprobadas = this.fotos.filter(f => f.estado === 'aprobada');
      this.fotosPendientes = this.fotos.filter(f => f.estado === 'pendiente');
    }else{
      console.log("Error al cargar las fotos: ", res.error);
    }
  });
}

eliminarFoto(foto: any) {
    if (!confirm('Confirma que quiere eliminar la foto')) return;

    this.fotosService.eliminarFoto(foto.id_foto).subscribe({
    next: () => {
        this.cargarFotos();
    },
  error: (error) => 
    console.error('Error al eliminar la foto: ', error)
  });
  }

cambiarEstado(foto: any, nuevoEstado: 'aprobada' | 'rechazada') {
  const confirmacion = confirm(`¿Estás seguro de que deseas marcar esta foto como ${nuevoEstado}?`);
  if (!confirmacion) return;

  this.fotosService.cambiarEstadoFoto(foto.id_foto, nuevoEstado).subscribe({
    next: () => {
        this.cargarFotos();
    },
    error: (error) => 
      console.error('Error al cambiar estado de la foto: ', error)
    });
}


onFileSelected(event: Event): void {
  this.inputFile = event.target;
    this.fImagen = (event.target as HTMLInputElement).files![0];

    var reader = new FileReader();
    reader.onloadend = ()=> {  // Debe ser una función arrow para que conserve el ámbito (y encuente a this.imagen64)
      // console.log('contenido: ', reader.result);
      // Mostramos la imagen en el elemento img:
      this.imagen64 = <string>reader.result;
      // this.imagen64 = reader.result as string;
    }
    this.fotoSeleccionado=true;
    reader.readAsDataURL(this.fImagen);
}

subirFoto(): void {
  if (this.fotoForm.valid && this.fotoSeleccionado) {
    this.fotosService.subirFoto(
                            this.fotoForm.value.nombre_foto, 
                            this.fotoForm.value.descripcion, 
                            this.imagen64, 
                            this.id_usuario, 
                            this.id_desafio
                          ).subscribe({
        next: () => {
          this.fotoForm.reset();
          this.showForm=false;
          this.fotoSeleccionado=false;
        },
        error: (error) => 
          console.error('Error al subir la foto: ', error)
    });
  }
}

editarFoto(){
  console.log(this.fotoFormEditar.value);
  this.fotosService.modificarFoto(
                            this.idFotoEditar,
                            this.fotoFormEditar.value.nombre_foto, 
                            this.fotoFormEditar.value.descripcion, 
                          ).subscribe({
        next: () => {
          this.fotoForm.reset();
          this.showFormEditar=false;
          this.fotoSeleccionado=false;
          this.cargarFotos();
        },
        error: (error) => 
          console.error('Error al subir la foto: ', error)
  });
}
}
