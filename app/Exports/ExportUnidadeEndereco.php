<?php

namespace App\Exports;

use App\Models\Correios\ModelsAuxiliares\UnidadeEndereco;
use Maatwebsite\Excel\Concerns\FromCollection;


class ExportUnidadeEndereco implements FromCollection {
    /**
    * @return \Illuminate\Support\Collection
    *php artisan make:export ExportAlarmes --model=\Models\Unidades\Alarme
    *Export created successfully.
    */
    public function collection(){
        //return User::get();
        return UnidadeEndereco::all();
    }
}
