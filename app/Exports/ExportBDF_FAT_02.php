<?php

namespace App\Exports;

use App\Models\Correios\ModelsAuxiliares\BDF_FAT_02;
use Maatwebsite\Excel\Concerns\FromCollection;


class ExportBDF_FAT_02 implements FromCollection {
    /**
    * @return \Illuminate\Support\Collection
    *php artisan make:export ExportAlarmes --model=\Models\Unidades\Alarme
    *Export created successfully.
    */
    public function collection(){
        //return User::get();
        return BDF_FAT_02::all();
    }
}
