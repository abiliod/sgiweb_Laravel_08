<?php

namespace App\Exports;

use App\Models\Correios\ModelsAuxiliares\SMBxBDF_NaoConciliado;
use Maatwebsite\Excel\Concerns\FromCollection;



class ExportSMBxBDF_NaoConciliado implements FromCollection {
    /**
    * @return \Illuminate\Support\Collection
    *php artisan make:export ExportAlarmes --model=\Models\Unidades\Alarme
    *Export created successfully.
    */
    public function collection(){
        //return User::get();
        return SMBxBDF_NaoConciliado::all();
    }
}


