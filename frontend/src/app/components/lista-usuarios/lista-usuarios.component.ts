import { Component } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Usuario } from '../../models/usuario';
import { UsuariosService } from '../../services/usuarios.service';
import { ActivatedRoute, RouterLink } from '@angular/router';


@Component({
  selector: 'app-lista-usuarios',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './lista-usuarios.component.html',
  styleUrls: ['./lista-usuarios.component.css']
})

export class ListaUsuariosComponent {
  showForm = false;
  usuarioForm: FormGroup;
  usuarios: Usuario[] = [];
  idUsuarioEditando: number = 0;
  id_usuario: number=0;

  constructor(private fb: FormBuilder, private usuarioService: UsuariosService, private route: ActivatedRoute ) {
    this.usuarioForm = this.fb.group({
      nombre: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      rol: ['', Validators.required],
      fecha_creacion: ['', Validators.required],
      activo: [true]
    });
    this.listarUsuarios();
  }

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id_usuario');
    if(idParam){
      this.id_usuario = parseInt(idParam, 10);
    }
    console.log(this.id_usuario)
  }

  listarUsuarios() {
    this.usuarioService.listarUsuarios().subscribe({
      next: (data: any) => {
        this.usuarios = data.usuarios;
        console.log(data);
      },
      error: (err) => {
        console.error('Error al listar usuarios: ', err);
      }
    });
  }

  toggleForm() {
    this.showForm = !this.showForm;
    if (!this.showForm) {
      this.usuarioForm.reset({ rol: 'Usuario', activo: true });
      this.idUsuarioEditando = 0;
    }
  }

  editarUsuario(usuario: any) {
    this.usuarioForm.patchValue(usuario);
    this.showForm = !this.showForm;
    this.idUsuarioEditando = usuario.id_usuario;
  }

  onSubmit() {
    if (this.usuarioForm.invalid) {
      console.log('Formulario inválido');
      return;
    }
    this.usuarioService.modificarUsuario(
      this.idUsuarioEditando,
      this.usuarioForm.value.nombre,
      this.usuarioForm.value.email,
      this.usuarioForm.value.rol,
      this.usuarioForm.value.activo
    ).subscribe({
      next: (res) => {
        console.log('Usuario modificado: ', res);
        this.listarUsuarios();
        this.usuarioForm.reset();
        this.showForm = false;
      },
      error: (err) => {
        console.error('Error al editar usuario: ', err);
      }
    });
  }

  eliminarUsuario(usuario: any) {
    this.usuarioService.eliminarUsuario(usuario.id_usuario).subscribe({
      next: () => {
        this.listarUsuarios();
      },
      error: (err) => {
        console.error('Error al eliminar usuario: ', err);
      }
    });
  }
}