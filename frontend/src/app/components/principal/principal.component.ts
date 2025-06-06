import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { DesafiosService } from '../../services/desafios.service';
import { Desafio } from '../../models/desafio';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { UsuariosService } from '../../services/usuarios.service';

@Component({
  selector: 'app-principal',
  imports: [ ReactiveFormsModule, RouterLink],
  templateUrl: './principal.component.html',
  styleUrls: ['./principal.component.css']
  
})
export class PrincipalComponent {
  rolUsuario = '';
  showForm = false;
  desafioForm: FormGroup;
  desafios: Desafio[] = [];
  editando: boolean = false;
  idDesafioEditando: number = 0;
  id_usuario: number = 0;
  fotoSeleccionado: boolean = false;
  fImagen: File | null = null;
  inputFile: any = null;
  imagen64:string = "";
  errorMessage: string = "";


  constructor(private fb: FormBuilder, private desafioService: DesafiosService, private usuarioService: UsuariosService, private route: ActivatedRoute) {
    this.desafioForm = this.fb.group({
      nombre: ['', Validators.required],
      descripcion: ['', Validators.required],
      fecha_inicio: ['', Validators.required],
      fecha_fin: ['', Validators.required]
    });
    this.listarDesafios();
  }

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id_usuario');
    if(idParam){
      this.id_usuario = parseInt(idParam, 10);
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
  }
  
  listarDesafios(){
    this.desafioService.listarDesafio().subscribe({
      next: (data: any) => {
        this.desafios=data.desafios;
      },
    error: (error) => 
        console.error('Error al listar desafios: ', error)
    });
  }

  toggleForm() {
    this.showForm = !this.showForm;
    this.errorMessage = "";

  }

  modificar(desafio: any) {
    if (this.showForm){
      this.desafioForm.reset();
    }else{
      this.desafioForm.patchValue(desafio);
    }
    this.editando = !this.editando;
    this.toggleForm();
    this.idDesafioEditando = desafio.id_desafio;
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

  onSubmit() {
    if(this.desafioForm.invalid){
        this.errorMessage = "Todos los campos son obligatorios";
        return;
    }
    if(this.desafioForm.value.fecha_inicio > this.desafioForm.value.fecha_fin){
      this.errorMessage = "La fecha de inicio no puede ser mayor que la fecha de fin";
      return;
    }
    if(this.editando){
      this.desafioService.modificarDesafio(
                                          this.idDesafioEditando,
                                          this.desafioForm.value.nombre, 
                                          this.desafioForm.value.descripcion, 
                                          this.desafioForm.value.fecha_inicio, 
                                          this.desafioForm.value.fecha_fin
                                          ).subscribe({
      next: () => {
          this.listarDesafios(); // regenera la lista con los nuevos desafios
          this.desafioForm.reset();
          this.idDesafioEditando = 0
          this.showForm = false;
          this.editando = false;
          this.errorMessage="";
        },
        error: (err) => {
          console.error('Error al editar desafio: ', err);
        }
      });
    }else{
      if (!this.fotoSeleccionado) {
        alert('Formulario incompleto, la foto es obligatoria');
        return;
      }
      this.desafioService.subirFoto(
                                      this.desafioForm.value.nombre, 
                                      this.desafioForm.value.descripcion, 
                                      this.desafioForm.value.fecha_inicio, 
                                      this.desafioForm.value.fecha_fin, 
                                      this.imagen64,
                                    ).subscribe({
        next: (res) => {
          console.log('Desafio creado: ', res);
          this.listarDesafios(); // regenera la lista con los nuevos desafios
          this.desafioForm.reset();
          this.showForm = false;
          this.fotoSeleccionado=false;
          this.errorMessage="";
        },
        error: (err) => {
          console.error('Error al crear desafio: ', err);
        }
      });
      }
  }

  eliminarDesafio(desafio: any){
    this.desafioService.eliminarDesafio(desafio.id_desafio).subscribe({
        next: () => {
          this.listarDesafios(); // regenera la lista con los nuevos desafios
        },
        error: (err) => {
          console.error('Error al eliminar desafio: ', err);
        }
      });
  }
  
}

