<?php
namespace App\Imports;
use App\Models\Correios\Alarme;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportAlarmes implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new Alarme([
            'Cliente'      => $row['Cliente'],
            'Armedesarme' => $row['Arme/Desarme'],
            'data' => $row['data'],
            'hora' => $row['hora'],
            'usuario' => $row['usuario'],
            'mcu' => $row['mcu'],
            'matricula' => $row['matricula'],
        ]);
    }
}



