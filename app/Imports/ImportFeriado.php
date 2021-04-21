<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\Feriado;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportFeriado implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new Feriado([
            'Cliente'      => $row['Cliente'],
            'Armedesarme' => $row['Arme/Desarme'],
            'data' => $row['data'],
            'hora' => $row['hora'],
        ]);
    }
}



