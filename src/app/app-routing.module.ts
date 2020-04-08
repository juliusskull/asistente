import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { BookDataComponent } from './book-data/book-data.component';
import { Creador1ListComponent } from 'src/app/components/creador1/creador1.component';
import { CreadorNewComponent } from 'src/app/components/creadorNew/creadorNew.component';

const routes: Routes = [
  { path: 'books', component: BookDataComponent },
  {path: 'creador1', component: Creador1ListComponent}
 ,{path: 'creador-new', component: CreadorNewComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
