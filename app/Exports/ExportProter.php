<?php

namespace App\Exports;

use App\Models\Correios\ModelsAuxiliares\Proter;
use Maatwebsite\Excel\Concerns\FromCollection;


class ExportProter implements FromCollection {
    /**
    * @return \Illuminate\Support\Collection
    *php artisan make:export ExportAlarmes --model=\Models\Unidades\Alarme
    *Export created successfully.
    */
    public function collection(){
        //return User::get();
        return Proter::all();
    }
}


