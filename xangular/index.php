<?php
/**
 * Created by PhpStorm.
 * User: jgutierrez
 * Date: 18/02/20
 * Time: 7:31
 */
//$url_servicio="http://192.168.110.32/api_compras/v0/web/remitos-solicitudes-oc/OC/4812";
$url="";

if($_GET['url']){
    $url =  $_GET['url'];
}
$p_titulo="";

if($_GET['titulo']){
    $p_titulo =  lcfirst ($_GET['titulo']);
}
$pk= array();

if(isset($_GET['pk1'])){
    $p_pk1 =   $_GET['pk1'];
    array_push($pk,$p_pk1);
}
if(isset($_POST['pk2'])){
    $p_pk2 =  $_POST['pk2'];
    array_push($pk,$p_pk2);
}
if(isset($_POST['pk3'])){
    $p_pk3 =  $_POST['pk3'];
    array_push($pk,$p_pk3);
}
if(isset($_POST['pk4'])){
    $p_pk4 =  $_POST['pk4'];
    array_push($pk,$p_pk4);
}


$url_servicio= $url;// "http://192.168.110.32/api_compras/v0/web/remitosoc";
//$res = file_get_contents($url_servicio);
$res='[
{
"id": 1,
"titulo": "prueba",
"pk1": "a",
"pk2": "b",
"pk3": "c",
"pk4": "d",
"jsontxt": "{\"titulo\":\"prueba\"}",
"fchalta": "2020-04-03T02:13:55.000Z"
}
]';

$titulo= $p_titulo;// "remitooc";

$aa= json_decode($res);
$bb= $aa->data;
//print_r($bb[0]);
$atributos=[];
$tipos=[];

foreach ($bb[0] as $clave=>$valor)
{


    array_push($atributos,$clave);
	echo "$clave=>$valor</p>";
    if(is_numeric($valor)){
        array_push($tipos,"number");
    }else{
        array_push($tipos,"string");
    }
	
}

deleteDirectory("salida");
mkdir("salida");
if (!file_exists("salida/interfaces")) {
mkdir("salida/interfaces");
}
if (!file_exists("salida/services")) {
mkdir("salida/services");
}
if (!file_exists("salida/components")) {
    mkdir("salida/components");
}
if (!file_exists("salida/components/$titulo")) {
mkdir("salida/components/$titulo");
}
/*
if (!file_exists($dir_src.'/db')) {
    mkdir($dir_src.'/db', 0777, true);
}
*/
$fp = fopen("salida/interfaces/". $titulo.".interface.ts", "w");
fwrite($fp,getInterface($atributos,$tipos,$titulo));


$fp = fopen("salida/services/". $titulo.".service.ts", "w");
fwrite($fp,getService($titulo,$url_servicio, ucfirst($titulo)));

$fp = fopen("salida/components/$titulo/". $titulo.".component.ts", "w");
fwrite($fp,getComponet($titulo,$titulo,$titulo,$atributos,$pk));

$fp = fopen("salida/components/$titulo/". $titulo.".component.html", "w");
fwrite($fp,getHtml($titulo,$atributos));

$host= $_SERVER["HTTP_HOST"];
$url= $_SERVER["REQUEST_URI"];
$archivo=  "p". date("Ymdhms");




zip_creation("salida", "temp/$archivo");

$archivo="http://".$host.$url."temp/".$archivo.".zip";
echo $archivo;



function getInterface($atributos,$tipos,$titulo){
    $s="export interface ".ucfirst($titulo)." {"."\r\n";
    for($i=0;$i<count($atributos);$i++){
        $s.=$atributos[$i] . "?: ".$tipos[$i].";". "\r\n";
    }
    $s.="}"."\r\n";
    return $s;
}
function getService($titulo,$url_servicio,$interface){
    $s="import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { ParentComprasService } from './parentCompras.service';
import { ".$interface." } from '../interfaces/".strtolower($interface).".interface';
import { hostServiciosBack, proyectoServiciosBack } from '../shared/constants/globals';
import {DropdownModule} from 'primeng/dropdown';
@Injectable({
  providedIn: 'root'
})
export class ".ucfirst($titulo)."Service extends ParentComprasService{
    token: any = null;
    httpHeaders: HttpHeaders;
    url: string;
    items: any[];
constructor(public _http: HttpClient) {
    super(_http);
    this.url = '".$url_servicio."';//hostServiciosBack + proyectoServiciosBack + 'direccion';
 }

 get".ucfirst($titulo)."s() {
    let url".ucfirst($titulo)." = this.url ;

    return this._http.get<any>(url".ucfirst($titulo).", { headers: this.reqHeader })
                .toPromise()
                .then(res => res.data as ".$interface."[])
                .then(data => data);
  }
    save".ucfirst($titulo)."(items: ".ucfirst($titulo).",  editMode: boolean) {

       let json = JSON.stringify(items);
       let params = json;
       let urlSave = this.url ;
       if (editMode) {
       /*
         console.log ('Actualizando');
         urlSave = urlSave + '/' + items.id_$titulo;
         return this._http.put(urlSave, params, { headers: this.reqHeader });
         */
      } else {
         console.log ('Guardando');
         return this._http.post(urlSave, params, { headers: this.reqHeader });
      }
    }

   delete".ucfirst($titulo)."(items: ".ucfirst($titulo).") {

    let json = JSON.stringify(items);
    let params = json;
    let urlSave = this.url + '/delete/' + this.usuario ;
    return this._http.post(urlSave, params, { headers: this.reqHeader });

    }
}
";
    return $s;
}

function getComponet($titulo,$interface,$service,$atributos,$pk){
    $col="";
    $col_v="";
    $col_v2="";
    $col_validacion="this.userform = this.fb.group({"."\r\n";
    foreach($atributos as  $valor){
        if(strpos(strtoupper($valor),"ID_")>0){
            $col.="{ field: '".$valor."', header: '".str_replace("_"," ",ucfirst($valor))."', width: '5%' },"."\r\n";
        }else{
            $col.="{ field: '".$valor."', header: '".str_replace("_"," ",ucfirst($valor))."', width: '20%'},"."\r\n";
        }
        $col_v.= "  nuevo".$valor .": any;\r\n";
        $col_v2=  "$valor: this.nuevo$valor," ."\r\n";

        $col_validacion.="'$valor': new FormControl('', Validators.required),"."\r\n";
    }
    $col_validacion.=  "})";

    $filtro_borrar="";
    foreach( $pk as $c){
        if(strlen($filtro_borrar)>0){
            $filtro_borrar.= " && ";
        }
        $filtro_borrar.="val.$c !== this.o".ucfirst($titulo)."Selected.$c";
    }

    $s="import { Component, OnInit } from '@angular/core';
import {". ucfirst($interface)." } from '../../interfaces/".strtolower($interface) .".interface';
import { ActivatedRoute, Router, Params } from '@angular/router';
import { MessageService, ConfirmationService } from 'primeng/primeng';
import { FormGroup, FormControl, Validators, FormArray, FormBuilder } from '@angular/forms';
import { ".ucfirst($interface)."Service } from '../../services/".strtolower($service) .".service';
import {SelectItem} from 'primeng/api';

@Component({
  selector: 'app-$titulo-list',
  providers: [ConfirmationService, MessageService],
  templateUrl: './".$titulo.".component.html'
})
export class ".ucfirst($titulo)."ListComponent implements OnInit {
    userform:  FormGroup;
    submitted: boolean;
    modoLista: boolean;

    o".ucfirst($titulo)."sList: ".ucfirst($titulo)."[];
    cols".ucfirst($titulo)."s: any[];
    o".ucfirst($titulo)."Selected: ".ucfirst($interface) .";
    oNuevo: ".ucfirst($interface) .";
    display: boolean;
    displayConfirmar: boolean;
    $col_v

  constructor(
      private fb: FormBuilder,
      private route: ActivatedRoute,
      private router: Router,
      private ".$service ."Service: ".ucfirst($service)."Service,
      private messageService: MessageService,
      private confirmationService: ConfirmationService,
    ) {   }

  ngOnInit() {
    this.modoLista = true;
    this.cols".ucfirst($titulo)."s = [
        $col
    ];

    this.".$service."Service.get".ucfirst($titulo)."s().then(cars => {
            this.o".ucfirst($titulo)."sList = cars;
        }
        );

        ".$col_validacion."
  }

  onRowSelectItem(){}

 onGuardar(): void {
   const nuevo: ".ucfirst($titulo)." = {
       $col_v2
    };
   this.".$titulo."Service.save".ucfirst($titulo)."(nuevo).subscribe(
    data => {
        let dato: any = data;
        console.log(dato.msg);
     },
    (err) => {
      console.log('Error al grabar expediente', err);
    }
  );

    this.o".ucfirst($titulo)."sList.push(nuevo);
    this.o".ucfirst($titulo)."sList = this.o".ucfirst($titulo)."sList.slice();

    this.display = false;

  }
   isRegSeleccionado(): boolean{
    if (!this.o".ucfirst($titulo)."Selected){
        this.messageService.add({ severity: 'warn', summary: 'Advertencia', detail: 'Debe seleccionar un registro', sticky: true });
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
    this.messageService.add({severity:'info', summary:'Success', detail:'Se guardaron los cambios'});
    this.modoLista = true;

}
    onBorrarClick(): void {
    if (this.isRegSeleccionado()){
        /* filtrar por la clave primaria */
        this.confirmationService.confirm({

            message: 'Esta por caducar el registro seleccionado'
        , accept: () => {
            this.onBorrar();
            }
    });
    }

  }
  onBorrar(): void {

        this.".$titulo."Service.delete".ucfirst($titulo)."(this.o".ucfirst($titulo)."Selected).subscribe(
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
        this.o".ucfirst($titulo)."sList = this.o".ucfirst($titulo)."sList.filter((val, i) =>
                $filtro_borrar
        );
  }
    onActualizar(): void{
    this.".$titulo."Service.get".ucfirst($titulo)."s().then(cars => {
        this.o".ucfirst($titulo)."sList = cars;
        this.o".ucfirst($titulo)."sList = this.o".ucfirst($titulo)."sList.slice();

    }
    );
  }
}
";
    return $s;
}
function getHtml($titulo,$atributos){
    $col="";
    $col2="";
    $nuevo_campos="";
    foreach($atributos as  $valor){
        $col.="<td> {{rowData.$valor}} </td>"."\r\n";
        $col2.=' <div class="ui-g-12 ui-md-6"><span class="md-inputfield"><input pinputtext="" type="text" class="ui-inputtext ui-corner-all ui-state-default ui-widget" pInputText [(ngModel)]="nuevo'.$valor.'" ><label>'.$valor.'</label></span></div>'."\r\n";

        $nuevo_campos.= getCampoForm($valor);
    }
    $messages="<p-toast [style]=\"{marginTop: '80px'}\" modal=\"true\"  position=\"top-center\" ></p-toast>
        <p-confirmDialog [style]=\"{width: '50vw'}\"></p-confirmDialog>";

    $nuevo='
        <p-dialog header="Nuevo '.$titulo.'" [(visible)]="display" modal="modal" showEffect="fade" [style]="{width: \'400px\'}">
        <div class="ui-g form-group">
            '. $nuevo_campos.'
          </div>


            <p-footer>
                <div class="ui-dialog-buttonpane ui-helper-clearfix">
                    <button type="button" pButton icon="pi pi-times" (click)="display=false" label="No"></button>
                    <button type="button" pButton icon="pi pi-check" (click)="onGuardar()" label="Yes"></button>
                </div>
            </p-footer>
        </p-dialog>';

    $s=$messages.'

<div  *ngIf="modoLista==true" class="ui-g">
            <p-toolbar>
                <div class="ui-toolbar-group-left">
                    <button (click)="onVer()"
                        pButton type="button"  label="Ver" icon="ui-icon-search" style="margin-right: 0.5em;"></button>
                    <button (click)="onVerAdd()"
                        pButton type="button"  label="Nuevo" icon="ui-icon-add" style="margin-right: 0.5em;"></button>
                    <button (click)="onBorrarClick()" pButton type="button" label="Caducar" icon="ui-icon-delete" style="margin-right: 0.5em;"></button>
                    <button type="button" pButton icon="ui-icon-refresh" iconPos="left" label="Actualizar" (click)="onActualizar()"
                    style="margin-right: 0.5em;"></button>
                    <button type="button" pButton icon="ui-icon-file-download" iconPos="left" label="CSV" (click)="dt.exportCSV()"
                        style="margin-right: 0.5em;"></button>
                </div>
                <div class="ui-toolbar-group-right">
                </div>
            </p-toolbar>

                <p-table #dt_unificados [columns]="cols'.ucfirst($titulo).'s" [value]="o'.ucfirst($titulo).'sList" selectionMode="single"
                 [(selection)]="o'.ucfirst($titulo).'Selected" dataKey="{{'.$atributos[0].'}}"  [paginator]="true" [rows]="10" [responsive]="true"
                 scrollWidth="100%" (onRowSelect)="onRowSelectItem()">
                        <ng-template pTemplate="caption">

                                <div style="text-align: center">
                                    '.ucfirst($titulo).'s Lista
                                    </div>
                         </ng-template>
                        <ng-template pTemplate="colgroup" let-columns>
                            <colgroup>
                                <col *ngFor="let col of columns" [style.width]="col.width">
                            </colgroup>
                        </ng-template>
                         <ng-template pTemplate="header" let-columns>
                             <tr>
                                 <th *ngFor="let col of columns" [pSortableColumn]="col.field" [style.text-align]="col.align">
                                   <b>  {{col.header}}</b>
                                     <!-- <p-sortIcon [field]="col.field"></p-sortIcon> -->
                                 </th>
                             </tr>
                         </ng-template>
                         <ng-template pTemplate="body" let-rowData let-columns="columns">
                             <tr [pSelectableRow]="rowData">
                                '.$col.'

                             </tr>
                         </ng-template>
                     </p-table>
        <!-- </div> -->


    </div>


<form *ngIf="modoLista==false" [formGroup]="userform" (ngSubmit)="onSubmit(userform.value)">
<p-panel header="Nuevo">
<div class="ui-grid ui-grid-responsive ui-grid-pad ui-fluid" style="margin: 10px 0px">
    '.$nuevo_campos.'
    <div class="ui-grid-row">
                                <div class="ui-grid-col-2"></div>
                              <div class="ui-grid-col-3">
                                <button pButton  label="cancelar" (click)="modoLista=true" ></button>

                            </div>
                        <div class="ui-grid-col-3">

                            <button pButton type="submit" label="Guardar" (click)="onGuardar()" [disabled]="!userform.valid"></button>
                        </div>
                                <div class="ui-grid-col-4"></div>
                            </div>
                        <div style="text-align:center;margin-top:20px" *ngIf="submitted">
                                Form Submitted
                                <br>

                            </div>
</div>
</p-panel>
</form>


';
    return $s;
}

function getHtmlVentana($titulo,$atributos){
    $col="";
    $col2="";
    $nuevo_campos="";
    foreach($atributos as  $valor){
        $col.="<td> {{rowData.$valor}} </td>"."\r\n";
        $col2.=' <div class="ui-g-12 ui-md-6"><span class="md-inputfield"><input pinputtext="" type="text" class="ui-inputtext ui-corner-all ui-state-default ui-widget" pInputText [(ngModel)]="nuevo'.$valor.'" ><label>'.$valor.'</label></span></div>'."\r\n";
        $nuevo_campos.= '<div class="ui-g-12 ui-md-6"><span class="md-inputfield"><input pinputtext="" type="text" class="ui-inputtext ui-corner-all ui-state-default ui-widget" pInputText [(ngModel)]="nuevo'.$valor.'" ><label>'.$valor.'</label></span></div>'."\r\n";
    }
    $messages="<p-toast [style]=\"{marginTop: '80px'}\" modal=\"true\"  position=\"top-center\" ></p-toast>
        <p-confirmDialog [style]=\"{width: '50vw'}\"></p-confirmDialog>";

    $nuevo='
        <p-dialog header="Nuevo '.$titulo.'" [(visible)]="display" modal="modal" showEffect="fade" [style]="{width: \'400px\'}">
        <div class="ui-g form-group">
            '. $nuevo_campos.'
          </div>


            <p-footer>
                <div class="ui-dialog-buttonpane ui-helper-clearfix">
                    <button type="button" pButton icon="pi pi-times" (click)="display=false" label="No"></button>
                    <button type="button" pButton icon="pi pi-check" (click)="onGuardar()" label="Yes"></button>
                </div>
            </p-footer>
        </p-dialog>';

    $s=$messages.'

<div class="ui-g">
            <p-toolbar>
                <div class="ui-toolbar-group-left">
                    <button (click)="onVer()"
                        pButton type="button"  label="Ver" icon="ui-icon-search" style="margin-right: 0.5em;"></button>
                    <button (click)="display=true"
                        pButton type="button"  label="Nuevo" icon="ui-icon-add" style="margin-right: 0.5em;"></button>
                    <button (click)="onBorrarClick()" pButton type="button" label="Caducar" icon="ui-icon-delete" style="margin-right: 0.5em;"></button>
                    <button type="button" pButton icon="ui-icon-refresh" iconPos="left" label="Actualizar" (click)="onActualizar()"
                    style="margin-right: 0.5em;"></button>
                    <button type="button" pButton icon="ui-icon-file-download" iconPos="left" label="CSV" (click)="dt.exportCSV()"
                        style="margin-right: 0.5em;"></button>
                </div>
                <div class="ui-toolbar-group-right">
                </div>
            </p-toolbar>

                <p-table #dt_unificados [columns]="cols'.ucfirst($titulo).'s" [value]="o'.ucfirst($titulo).'sList" selectionMode="single"
                 [(selection)]="o'.ucfirst($titulo).'Selected" dataKey="{{'.$atributos[0].'}}"  [paginator]="true" [rows]="10" [responsive]="true"
                 scrollWidth="100%" (onRowSelect)="onRowSelectItem()">
                        <ng-template pTemplate="caption">

                                <div style="text-align: center">
                                    '.ucfirst($titulo).'s Lista
                                    </div>
                         </ng-template>
                        <ng-template pTemplate="colgroup" let-columns>
                            <colgroup>
                                <col *ngFor="let col of columns" [style.width]="col.width">
                            </colgroup>
                        </ng-template>
                         <ng-template pTemplate="header" let-columns>
                             <tr>
                                 <th *ngFor="let col of columns" [pSortableColumn]="col.field" [style.text-align]="col.align">
                                   <b>  {{col.header}}</b>
                                     <!-- <p-sortIcon [field]="col.field"></p-sortIcon> -->
                                 </th>
                             </tr>
                         </ng-template>
                         <ng-template pTemplate="body" let-rowData let-columns="columns">
                             <tr [pSelectableRow]="rowData">
                                '.$col.'

                             </tr>
                         </ng-template>
                     </p-table>
        <!-- </div> -->


    </div>

            '.$nuevo.'





';
    return $s;
}

function deleteDirectory($dirPath) {
    if (is_dir($dirPath)) {
        $objects = scandir($dirPath);
        foreach ($objects as $object) {
            if ($object != "." && $object !="..") {
                if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                    deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        reset($objects);
        rmdir($dirPath);
    }
}
 function getCampoForm($valor){
     $titulo= str_replace( '_',' ', $valor);
     $pos = strpos(strtoupper($valor) , 'OBSERVACION');

     if($pos !== false ){
         return '<div class="ui-grid-row">
                        <div class="ui-grid-col-2"> '.$titulo.' *:</div>
                            <div class="ui-grid-col-6">
                                    <textarea pInputTextarea type="text" [(ngModel)]="nuevo'.$valor.'" formControlName="'.$valor.'" placeholder="Required"></textarea>
                            </div>
                            <div class="ui-grid-col-4">
                                    <p-message severity="error" text="'.$titulo.' is required" *ngIf="!userform.controls[\''.$valor.'\'].valid&&userform.controls[\''.$valor.'\'].dirty"></p-message>
                            </div>

                    </div>';
     }else{
         return ' <div class="ui-grid-row">
                <div class="ui-grid-col-2"> '.$titulo.' *:</div>
                    <div class="ui-grid-col-6">
                            <input pInputText type="text"  [(ngModel)]="nuevo'.$valor.'" formControlName="'.$valor.'" placeholder="Required"/>
                    </div>
                    <div class="ui-grid-col-4">
                            <p-message severity="error" text="'.$titulo.' is required" *ngIf="!userform.controls[\''.$valor.'\'].valid&&userform.controls[\''.$valor.'\'].dirty"></p-message>
                    </div>

            </div>';
     }


 }
 function zip_creation($source, $destination){
    $dir = opendir($source);
    $result = ($dir === false ? false : true);

    if ($result !== false) {


        $rootPath = realpath($source);

        // Initialize archive object
        $zip = new ZipArchive();
        $zipfilename = $destination.".zip";
        $zip->open($zipfilename, ZipArchive::CREATE | ZipArchive::OVERWRITE );

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        return TRUE;
    } else {
        return FALSE;
    }


}