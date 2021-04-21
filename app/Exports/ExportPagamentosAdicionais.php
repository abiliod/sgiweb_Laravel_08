<?php

namespace App\Exports;

use App\Models\Correios\ModelsAuxiliares\PagamentosAdicionais;
use Maatwebsite\Excel\Concerns\FromCollection;


class ExportPagamentosAdicionais implements FromCollection {
    /**
    * @return \Illuminate\Support\Collection
    *php artisan make:export ExportAlarmes --model=\Models\Unidades\Alarme
    *Export created successfully.
    */
    public function collection(){
        //return User::get();
        return PagamentosAdicionais::all();
    }
}
