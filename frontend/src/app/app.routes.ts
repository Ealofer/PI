import { Routes } from '@angular/router';
import { RegistroLoginComponent } from './components/registro-login/registro-login.component';
import { PrincipalComponent } from './components/principal/principal.component';
import { DestalleDesafioComponent } from './components/destalle-desafio/destalle-desafio.component';
import { ListaUsuariosComponent } from './components/lista-usuarios/lista-usuarios.component';

export const routes: Routes = [
    {
        path: '', component: RegistroLoginComponent
    },
    {
        path: 'principal/:id_usuario', component: PrincipalComponent
    },
    {
        path: 'detalle-desafio/:id_desafio/:id_usuario', component: DestalleDesafioComponent
    },
    {
        path: 'listaUsuarios/:id_usuario', component: ListaUsuariosComponent
    },
];