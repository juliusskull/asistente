import { Component, OnInit } from '@angular/core';
import {Creador1 } from '../../interfaces/creador1.interface';
import { ActivatedRoute, Router, Params } from '@angular/router';
//import { MessageService, ConfirmationService } from 'primeng/primeng';
import { FormGroup, FormControl, Validators, FormArray, FormBuilder } from '@angular/forms';
import { Creador1Service } from '../../service/creador1.service';
import {SelectItem} from 'primeng/api';

@Component({
  selector: 'app-creador1-list',
providers: [/*ConfirmationService, MessageService*/],
  templateUrl: './creador1.component.html'
})
export class Creador1ListComponent implements OnInit {
    userform:  FormGroup;
    submitted: boolean;
    modoLista: boolean;

    oCreador1sList: Creador1[];
    colsCreador1s: any[];
    oCreador1Selected: Creador1;
    oNuevo: Creador1;
    display: boolean;
    displayConfirmar: boolean;
    

  constructor(
      private fb: FormBuilder,
      private route: ActivatedRoute,
      private router: Router,
      private creador1Service: Creador1Service,
      /*private messageService: MessageService,
      private confirmationService: ConfirmationService,*/
    ) {   }

  ngOnInit() {
    this.modoLista = true;
    this.colsCreador1s = [
      { field: 'id', header: 'Id' },
      { field: 'titulo', header: 'Titulo' },
      { field: 'jsontxt', header: 'jsontxt' }
    ];

    this.creador1Service.getCreador1s().then(cars => {
         console.log("entro=>"+ cars.length );
           this.oCreador1sList = cars;

        //    let dato: any = cars;
         //   console.log(dato);
        }
        );

        

/*
        this.userform = this.fb.group({
})*/
  }

  onRowSelectItem(){}

 onGuardar(): void {
   const nuevo: Creador1 = {
       
    };
   this.creador1Service.saveCreador1(nuevo,false).subscribe(
    data => {
        let dato: any = data;
        console.log(dato.msg);
     },
    (err) => {
      console.log('Error al grabar expediente', err);
    }
  );

    this.oCreador1sList.push(nuevo);
    this.oCreador1sList = this.oCreador1sList.slice();

    this.display = false;

  }
   isRegSeleccionado(): boolean{
    if (!this.oCreador1Selected){
      //  this.messageService.add({ severity: 'warn', summary: 'Advertencia', detail: 'Debe seleccionar un registro', sticky: true });
        return false;
    } else {
        return true;
    }
  }
   onVer(): void {
    if (this.isRegSeleccionado()){

    }
  }
    onVerAdd(): void {

    this.modoLista =  false;
  }
  onSubmit(value: string) {
    this.submitted = true;
    //this.messageService.add({severity:'info', summary:'Success', detail:'Se guardaron los cambios'});
    this.modoLista = true;

}
    onBorrarClick(): void {
    if (this.isRegSeleccionado()){
        /* filtrar por la clave primaria */
        /*
        this.confirmationService.confirm({

            message: 'Esta por caducar el registro seleccionado'
        , accept: () => {
            this.onBorrar();
            }
    });*/
    }

  }
  onBorrar(): void {

        this.creador1Service.deleteCreador1(this.oCreador1Selected).subscribe(
            data => {
                let dato: any = data;
                console.log(dato.msg);
             },
            (err) => {
              console.log('Error al grabar caducar', err);
              this.onActualizar();
            }
          );

     /* filtrar por la clave primaria */
     /*
        this.oCreador1sList = this.oCreador1sList.filter((val, i) =>
                val.id !== this.oCreador1Selected.id
        );
        */
  }
    onActualizar(): void{
    this.creador1Service.getCreador1s().then(cars => {
        this.oCreador1sList = cars;
        this.oCreador1sList = this.oCreador1sList.slice();

    }
    );
  }
}
