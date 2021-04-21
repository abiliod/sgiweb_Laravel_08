<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\DebitoEmpregado;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportDebitoEmpregados implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new DebitoEmpregado([
            'cia'      => $row['cia'],
            'conta' => $row['Conta'],
            'data' => $row['Data'],
            'competencia' => $row['competencia'],
            'lote' => $row['Lote'],
            'tp' => $row['Tp'],
            'sto' => $row['MCU (Doc1)'],
            'nome_unidade' => $row['Nome Agência (Doc2)'],
            'historico' => $row['Histórico'],
            'valor' => $row['valor'],
            'observacoes' => $row['Observações'],
            'documento' => $row['Documento (Ref1)'],
            'matriculaEmpregado' => $row['Matrícula (Ref2)'],
            'nomeEmpregado' => $row['Nome Empregado (Ref3)'],
            'justificativa' => $row['Justificativa (Ad1)'],
            'regularizacao' => $row['Regularização'],
            'anexo' => $row['Anexo'],
            'acao' => $row['Ação'],

        ]);
    }
}



