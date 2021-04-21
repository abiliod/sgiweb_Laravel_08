<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\SgdoDistribuicao;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportSgdoDistribuicao implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new ImportSgdoDistribuicao([
            'dr'      => $row['dr'],
            'unidade'      => $row['unidade'],
            'mcu'      => $row['mcu'],
            'centralizadora'      => $row['centralizadora'],
            'mcu_centralizadora'      => $row['mcu_centralizadora'],
            'distrito'      => $row['distrito'],
            'area'      => $row['area'],
            'locomocao'      => $row['locomoo'],
            'funcionario'      => $row['funcionio'],
            'matricula'      => $row['matrula'],
            'data_incio_atividade'      => $row['data_inio_atividade'],
            'hora_incio_atividade'      => $row['hora_inio_atividade'],
            'data_saida'      => $row['data_saida'],
            'hora_saida'      => $row['hora_saida'],
            'data_retorno'      => $row['data_retorno'],
            'hora_retorno'      => $row['hora_retorno'],
            'data_tpc'      => $row['data_tpc'],
            'hora_do_tpc'      => $row['hora_do_tpc'],
            'data_termino_atividade'      => $row['data_termino_atividade'],
            'hora_termino_atividade'      => $row['hora_termino_atividade'],
            'justificado'      => $row['justificado'],
            'peso_da_bolsa_kg'      => $row['peso_da_bolsa_kg'],
            'peso_do_da_kg'      => $row['peso_do_da_kg'],
            'quantidade_de_da'      => $row['quantidade_de_da'],
            'quantidade_de_gu'      => $row['quantidade_de_gu'],
            'quantidade_de_objetos_qualificados'      => $row['quantidade_de_objetos_qualificados'],
            'quantidade_de_objetos_coletados'      => $row['quantidade_de_objetos_coletados'],
            'quantidade_de_pontos_de_entregacoleta'      => $row['quantidade_de_pontos_de_entregacoleta'],
            'quilometragem_percorrida'      => $row['quilometragem_percorrida'],
            'residuo_simples'      => $row['residuo_simples'],
            'residuo_qualificado'      => $row['residuo_qualificado'],
            'almoca_na_unidade'      => $row['almoca_na_unidade'],
            'compartilhado'      => $row['compartilhado'],
            'tipo_de_distrito'      => $row['tipo_de_distrito'],

        ]);
    }
}



