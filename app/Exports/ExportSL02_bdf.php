<?php

namespace App\Exports;

use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use Maatwebsite\Excel\Concerns\FromCollection;



class ExportSL02_bdf implements FromCollection {
    /**
    * @return \Illuminate\Support\Collection
    *php artisan make:export ExportAlarmes --model=\Models\Unidades\Alarme
    *Export created successfully.
    */
    public function collection(){
        //return User::get();
        return SL02_bdf::all();
    }
}


