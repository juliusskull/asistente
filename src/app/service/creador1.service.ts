import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { map } from 'rxjs/operators';
//import { ParentComprasService } from './parentCompras.service';
import { Creador1 } from '../interfaces/creador1.interface';
//import { hostServiciosBack, proyectoServiciosBack } from '../shared/constants/globals';
import {DropdownModule} from 'primeng/dropdown';
@Injectable({
  providedIn: 'root'
})
export class Creador1Service/* extends ParentComprasService*/{
    token: any = null;
    httpHeaders: HttpHeaders;
    url: string;
    items: any[];
constructor(public _http: HttpClient) {
   // super(_http);
    this.url = 'http://localhost:3001/creador1';//hostServiciosBack + proyectoServiciosBack + 'direccion';
 }

 getCreador1s() {
    let urlCreador1 = this.url ;

    return this._http.get<any>(urlCreador1/*, { headers: this.reqHeader }*/)
                .toPromise()
                .then(res => res.data as Creador1[])
                .then(data => data);
                /*
                .then(res => <Creador1[]>res.data)
                .then(data => { 
                  console.log("data->"+data.data);
                  return data; 
                });*/
  }
    saveCreador1(items: Creador1,  editMode: boolean) {

       let json = JSON.stringify(items);
       let params = json;
       let urlSave = this.url ;
       if (editMode) {
       /*
         console.log ('Actualizando');
         urlSave = urlSave + '/' + items.id_creador1;
         return this._http.put(urlSave, params, { headers: this.reqHeader });
         */
      } else {
         console.log ('Guardando');
         return this._http.post(urlSave, params/*, { headers: this.reqHeader }*/);
      }
    }

   deleteCreador1(items: Creador1) {

    let json = JSON.stringify(items);
    let params = json;
    let urlSave = this.url + '/delete/'  ;
    return this._http.post(urlSave, params/*, { headers: this.reqHeader }*/);
 
    }
   
}
