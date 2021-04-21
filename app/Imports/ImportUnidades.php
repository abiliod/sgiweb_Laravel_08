<?php
namespace App\Imports;
use App\Models\Correios\Unidade;
//use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\Importable;
//use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
//use Maatwebsite\Excel\Excel;

class ImportUnidades implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new Unidade([
            'tipoUnidade_id' => $row['tipoUnidade_id'],
            'se' => $row['se'],
            'seDescricao'  => $row['seDescricao'],
            'mcu'  => $row['mcu'],
            'an8'  => $row['an8'],
            'sto'  => $row['sto'],
            'status_unidadeDesc' => $row['status_unidadeDesc'],
            'status_unidade' => $row['status_unidade'],
            'descricao'  => $row['descricao'],
            'tipoOrgaoCod'  => $row['tipoOrgaoCod'],
            'tipoOrgaoDesc'  => $row['tipoOrgaoDesc'],
            'cnpj'  => $row['cnpj'],
            'categoria'  => $row['categoria'],
            'mecanizacao'  => $row['mecanizacao'],
            'faixaCepIni'  => $row['faixaCepIni'],
            'faixaCepFim'  => $row['faixaCepFim'],
            'tem_distribuicao'  => $row['tem_distribuicao'],
            'tipoEstrutura'  => $row['tipoEstrutura'],
            'quantidade_guiches'  => $row['quantidade_guiches'],
            'guiches_ocupados'  => $row['guiches_ocupados'],
            'ddd'  => $row['ddd'],
            'telefone'  => $row['telefone'],
            'mcu_subordinacaoAdm'  => $row['mcu_subordinacaoAdm'],
            'desc_subordinacaoAdm'  => $row['desc_subordinacaoAdm'],
            'nomeResponsavelUnidade'   => $row['nomeResponsavelUnidade'],
            'documentRespUnidade'  => $row['documentRespUnidade'],
            'email'  => $row['email'],
            'tipo_de_estrutura'  => $row['tipo_de_estrutura'],
            'subordinacao_tecnica'  => $row['subordinacao_tecnica'],
            'inicio_expediente'  => $row['inicio_expediente'],
            'final_expediente'  => $row['final_expediente'],
            'inicio_intervalo_refeicao' => $row['inicio_intervalo_refeicao'],
            'final_intervalo_refeicao' => $row['final_intervalo_refeicao'],
            'trabalha_sabado' => $row['trabalha_sabado'],
            'inicio_expediente_sabado' => $row['inicio_expediente_sabado'],
            'final_expediente_sabado' => $row['final_expediente_sabado'],
            'trabalha_domingo' => $row['trabalha_domingo'],
            'inicio_expediente_domingo' => $row['inicio_expediente_domingo'],
            'final_expediente_domingo' => $row['final_expediente_domingo'],
            'tem_plantao' => $row['tem_plantao'],
            'inicio_plantao_sabado' => $row['inicio_plantao_sabado'],
            'final_plantao_sabado' => $row['final_plantao_sabado'],
            'inicio_plantao_domingo' => $row['inicio_plantao_domingo'],
            'final_plantao_domingo' => $row['final_plantao_domingo'],
            'inicio_distribuicao' => $row['inicio_distribuicao'],
            'final_distribuicao' => $row['final_distribuicao'],
            'horario_lim_post_na_semana' => $row['horario_lim_post_na_semana'],
            'horario_lim_post_final_semana' => $row['horario_lim_post_final_semana'],
        ]);
    }
}



