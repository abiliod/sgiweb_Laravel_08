<?php

namespace App\Exports;

use App\Models\Correios\ModelsAuxiliares\SgdoDistribuicao;
use Maatwebsite\Excel\Concerns\FromCollection;


class ExportSgdoDistribuicao implements FromCollection {
    /**
    * @return \Illuminate\Support\Collection
    *php artisan make:export ExportAlarmes --model=\Models\Unidades\Alarme
    *Export created successfully.
    */
    public function collection(){
        //return User::get();
        return SgdoDistribuicao::all();
    }
}
