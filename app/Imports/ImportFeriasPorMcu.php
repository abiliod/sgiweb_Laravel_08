<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\FeriasPorMcu;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportFeriasPorMcu implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new FeriasPorMcu([
            'matricula'      => $row['matricula'],
            'nome' => $row['nome'],
            'lotacao' => $row['lotacao'],
            'funcao' => $row['funcao'],
            'dias' => $row['dias'],
            'inicio_fruicao' => $row['inicio_fruicao'],
            'termino_fruicao' => $row['termino_fruicao'],
        ]);
    }
}



