<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\Absenteismo;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportAbsenteismo implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new Absenteismo([
            'matricula'      => $row['matricula'],
            'nome'      => $row['nome'],
            'cargo'      => $row['cargo'],
            'lotacao'      => $row['lotacao'],
            'data_evento'      => $row['data_evento'],
            'dias'      => $row['dias'],
            'motivo'      => $row['motivo'],
        ]);
    }
}



