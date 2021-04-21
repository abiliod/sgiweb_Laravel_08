<?php


namespace App\Exports;


use App\Models\Correios\ModelsAuxiliares\Snci;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportSnci implements FromCollection
{
    public function collection(){
        //return User::get();
        return Snci::all();
    }
}
