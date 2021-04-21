<?php


namespace App\Exports;


use App\Models\Correios\ModelsDto\LancamentosSRO;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportLancamentosSRO implements FromCollection
{
use Exportable;
    public function collection()
    {
        return LancamentosSRO::all();
    }

}
