import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, FormArray, FormBuilder } from '@angular/forms';
import { Creador1Service } from '../../service/creador1.service';
import { ActivatedRoute, RouteReuseStrategy, Router } from '@angular/router';
import { MustMatch } from '../../helpers/mustMatch ';
@Component({
  selector: 'app-creadorNew',
  templateUrl: './creadorNew.component.html',
  styleUrls: ['./creadorNew.component.css']
})
export class CreadorNewComponent implements OnInit {
  userform:  FormGroup;
  nuevotitulo: any;
  nuevojsontxt:any;
  constructor(
    
    private fb: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
      private creador1Service: Creador1Service,
  ) { }

  ngOnInit() {

    this.userform = this.fb.group({
      'titulo': new FormControl('', Validators.required),
      'jsontxt': new FormControl('', Validators.required),
      
      })
  }
  get f() { return this.userform.controls; }
  onSubmit(value: string) {
    
  //  this.submitted = true;

    // stop here if form is invalid
    if (this.userform.invalid) {
        return;
    }
    alert('xx');
  }
}
