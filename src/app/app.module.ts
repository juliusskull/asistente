import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { BookDataComponent } from './book-data/book-data.component';
import {TableModule} from 'primeng/table';

import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { Creador1ListComponent } from 'src/app/components/creador1/creador1.component';
import {CardModule} from 'primeng/card';
import { TabViewModule } from 'primeng/components/tabview/tabview';
import { SharedModule, PanelModule } from 'primeng/primeng';
import { CreadorNewComponent } from 'src/app/components/creadorNew/creadorNew.component';

@NgModule({
  declarations: [
    AppComponent,
    BookDataComponent,
    Creador1ListComponent,
    CreadorNewComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    TableModule,
    FormsModule,
     ReactiveFormsModule,
    HttpClientModule,
    CardModule,TabViewModule
    ,SharedModule, PanelModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
