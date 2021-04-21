<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Permissao;

class PermissaoTableSeeder extends Seeder {
/**
* Run the database seeds.
*
* @return void
*/
public function run() {
//    DB::table('permissaos')->truncate(); //excluir e zerar a tabela

    if(!Permissao::where('nome','=','grupoverificacao_listar')->count()) {
        Permissao::create([
            'nome'=>'grupoverificacao_listar',
            'descricao'=>'Listar Grupo de verificação'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','grupoverificacao_listar')->first();
        $permissao->update([
            'nome'=>'grupoverificacao_listar',
            'descricao'=>'Listar Grupo de verificação'
        ]);
    }

    if(!Permissao::where('nome','=','grupoverificacao_adicionar')->count()) {
        Permissao::create([
            'nome'=>'grupoverificacao_adicionar',
            'descricao'=>'Adicionar Grupo de verificação'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','grupoverificacao_adicionar')->first();
        $permissao->update([
            'nome'=>'grupoverificacao_adicionar',
            'descricao'=>'Adicionar Grupo de verificação'
        ]);
    }

    if(!Permissao::where('nome','=','grupoverificacao_editar')->count()) {
        Permissao::create([
            'nome'=>'grupoverificacao_editar',
            'descricao'=>'Editar Grupo de verificação'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','grupoverificacao_editar')->first();
        $permissao->update([
            'nome'=>'grupoverificacao_editar',
            'descricao'=>'Editar Grupo de verificação'
        ]);
    }

    if(!Permissao::where('nome','=','grupoverificacao_deletar')->count()) {
        Permissao::create([
            'nome'=>'grupoverificacao_deletar',
            'descricao'=>'Excluir Grupo de verificação'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','grupoverificacao_deletar')->first();
        $permissao->update([
            'nome'=>'grupoverificacao_deletar',
            'descricao'=>'Excluir Grupo de verificação'
        ]);
    }

    if(!Permissao::where('nome','=','inspecao_listar')->count()) {
        Permissao::create([
            'nome'=>'inspecao_listar',
            'descricao'=>'Listar Inspeção'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','inspecao_listar')->first();
        $permissao->update([
            'nome'=>'inspecao_listar',
            'descricao'=>'Listar Inspeção'
        ]);
    }

    if(!Permissao::where('nome','=','inspecao_adicionar')->count()) {
        Permissao::create([
            'nome'=>'inspecao_adicionar',
            'descricao'=>'Adicionar Inspeção'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','inspecao_adicionar')->first();
        $permissao->update([
            'nome'=>'inspecao_adicionar',
            'descricao'=>'Adicionar Inspeção'
        ]);
    }

    if(!Permissao::where('nome','=','inspecao_editar')->count()) {
        Permissao::create([
            'nome'=>'inspecao_editar',
            'descricao'=>'Editar Inspeção'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','inspecao_editar')->first();
        $permissao->update([
            'nome'=>'inspecao_editar',
            'descricao'=>'Editar Inspeção'
        ]);
    }

    if(!Permissao::where('nome','=','inspecao_deletar')->count()) {
        Permissao::create([
            'nome'=>'inspecao_deletar',
            'descricao'=>'Excluir Inspeção'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','inspecao_deletar')->first();
        $permissao->update([
            'nome'=>'inspecao_deletar',
            'descricao'=>'Excluir Inspeção'
        ]);
    }

    if(!Permissao::where('nome','=','pagina_listar')->count()) {
        Permissao::create([
            'nome'=>'pagina_listar',
            'descricao'=>'Listar Página'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pagina_listar')->first();
        $permissao->update([
            'nome'=>'pagina_listar',
            'descricao'=>'Listar Página'
        ]);
    }

    if(!Permissao::where('nome','=','pagina_adicionar')->count()) {
        Permissao::create([
            'nome'=>'pagina_adicionar',
            'descricao'=>'Adicionar Página'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pagina_adicionar')->first();
        $permissao->update([
            'nome'=>'pagina_adicionar',
            'descricao'=>'Adicionar Página'
        ]);
    }

    if(!Permissao::where('nome','=','pagina_editar')->count()) {
        Permissao::create([
            'nome'=>'pagina_editar',
            'descricao'=>'Editar Página'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pagina_editar')->first();
        $permissao->update([
            'nome'=>'pagina_editar',
            'descricao'=>'Editar Página'
        ]);
    }

    if(!Permissao::where('nome','=','pagina_deletar')->count()) {
        Permissao::create([
            'nome'=>'pagina_deletar',
            'descricao'=>'Deletar Página'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pagina_deletar')->first();
        $permissao->update([
            'nome'=>'pagina_deletar',
            'descricao'=>'Deletar Página'
        ]);
    }

    if(!Permissao::where('nome','=','slide_listar')->count()) {
        Permissao::create([
            'nome'=>'slide_listar',
            'descricao'=>'Listar Slide'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','slide_listar')->first();
        $permissao->update([
            'nome'=>'slide_listar',
            'descricao'=>'Listar Slide'
        ]);
    }

    if(!Permissao::where('nome','=','slide_adicionar')->count()) {
        Permissao::create([
            'nome'=>'slide_adicionar',
            'descricao'=>'Adicionar Slide'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','slide_adicionar')->first();
        $permissao->update([
            'nome'=>'slide_adicionar',
            'descricao'=>'Adicionar Slide'
        ]);
    }

    if(!Permissao::where('nome','=','slide_editar')->count()) {
        Permissao::create([
            'nome'=>'slide_editar',
            'descricao'=>'Editar Slide'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','slide_editar')->first();
        $permissao->update([
            'nome'=>'slide_editar',
            'descricao'=>'Editar Slide'
        ]);
    }

    if(!Permissao::where('nome','=','slide_deletar')->count()) {
        Permissao::create([
            'nome'=>'slide_deletar',
            'descricao'=>'Deletar Slide'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','slide_deletar')->first();
        $permissao->update([
            'nome'=>'slide_deletar',
            'descricao'=>'Deletar Slide'
        ]);
    }

    if(!Permissao::where('nome','=','papel_listar')->count()) {
        Permissao::create([
            'nome'=>'papel_listar',
            'descricao'=>'Listar Papéis'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','papel_listar')->first();
        $permissao->update([
            'nome'=>'papel_listar',
            'descricao'=>'Listar Papéis'
        ]);
    }

    if(!Permissao::where('nome','=','papel_adicionar')->count()) {
        Permissao::create([
            'nome'=>'papel_adicionar',
            'descricao'=>'Adicionar Papéis'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','papel_adicionar')->first();
        $permissao->update([
            'nome'=>'papel_adicionar',
            'descricao'=>'Adicionar Papéis'
        ]);
    }

    if(!Permissao::where('nome','=','papel_editar')->count()) {
        Permissao::create([
            'nome'=>'papel_editar',
            'descricao'=>'Editar Papéis'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','papel_editar')->first();
        $permissao->update([
            'nome'=>'papel_editar',
            'descricao'=>'Editar Papéis'
        ]);
    }

    if(!Permissao::where('nome','=','papel_deletar')->count()) {
        Permissao::create([
            'nome'=>'papel_deletar',
            'descricao'=>'Deletar Papéis'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','papel_deletar')->first();
        $permissao->update([
            'nome'=>'papel_deletar',
            'descricao'=>'Deletar Papéis'
        ]);
    }

    if(!Permissao::where('nome','=','compliance_listar_importacoes')->count()) {
        Permissao::create([
            'nome'=>'compliance_listar_importacoes',
            'descricao'=>'Compliance listar importacoes'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','compliance_listar_importacoes')->first();
        $permissao->update([
            'nome'=>'compliance_listar_importacoes',
            'descricao'=>'Compliance listar importacoes'
        ]);
    }

    if(!Permissao::where('nome','=','unidade_listar')->count()) {
        Permissao::create([
            'nome'=>'unidade_listar',
            'descricao'=>'Listar Unidade'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','unidade_listar')->first();
        $permissao->update([
            'nome'=>'unidade_listar',
            'descricao'=>'Listar Unidade'
        ]);
    }

    if(!Permissao::where('nome','=','unidade_adicionar')->count()) {
        Permissao::create([
            'nome'=>'unidade_adicionar',
            'descricao'=>'Adicionar Unidade'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','unidade_adicionar')->first();
        $permissao->update([
            'nome'=>'unidade_adicionar',
            'descricao'=>'Adicionar Unidade'
        ]);
    }

    if(!Permissao::where('nome','=','unidade_editar')->count()) {
        Permissao::create([
            'nome'=>'unidade_editar',
            'descricao'=>'Editar Unidade'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','unidade_editar')->first();
        $permissao->update([
            'nome'=>'unidade_editar',
            'descricao'=>'Editar Unidade'
        ]);
    }

    if(!Permissao::where('nome','=','unidade_deletar')->count()) {
        Permissao::create([
            'nome'=>'unidade_deletar',
            'descricao'=>'Excluir Unidade'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','unidade_deletar')->first();
        $permissao->update([
            'nome'=>'unidade_deletar',
            'descricao'=>'Excluir Unidade'
        ]);
    }

    if(! Permissao::where('nome','=','usuario_listar')->count() ) {
        Permissao::create([
            'nome'=>'usuario_listar',
            'descricao'=>'Listar Usuários'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','usuario_listar')->first();
        $permissao->update([
            'nome'=>'usuario_listar',
            'descricao'=>'Listar Usuários'
        ]);
    }

    if(!Permissao::where('nome','=','usuario_adicionar')->count()) {
        Permissao::create([
            'nome'=>'usuario_adicionar',
            'descricao'=>'Adicionar Usuários'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','usuario_adicionar')->first();
        $permissao->update([
            'nome'=>'usuario_adicionar',
            'descricao'=>'Adicionar Usuários'
        ]);
    }

    if(!Permissao::where('nome','=','usuario_editar')->count()) {
        Permissao::create([
            'nome'=>'usuario_editar',
            'descricao'=>'Editar Usuários'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','usuario_editar')->first();
        $permissao->update([
            'nome'=>'usuario_editar',
            'descricao'=>'Editar Usuários'
        ]);
    }

    if(!Permissao::where('nome','=','usuario_deletar')->count()) {
        Permissao::create([
            'nome'=>'usuario_deletar',
            'descricao'=>'Deletar Usuários'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','usuario_deletar')->first();
        $permissao->update([
            'nome'=>'usuario_deletar',
            'descricao'=>'Deletar Usuários'
        ]);
    }

    if(!Permissao::where('nome','=','pessoa_listar')->count()) {
        Permissao::create([
            'nome'=>'pessoa_listar',
            'descricao'=>'Listar Pessoas'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pessoa_listar')->first();
        $permissao->update([
            'nome'=>'pessoa_listar',
            'descricao'=>'Listar Pessoas'
        ]);
    }

    if(!Permissao::where('nome','=','pessoa_adicionar')->count()) {
        Permissao::create([
            'nome'=>'pessoa_adicionar',
            'descricao'=>'Adicionar Pessoas'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pessoa_adicionar')->first();
        $permissao->update([
            'nome'=>'pessoa_adicionar',
            'descricao'=>'Adicionar Pessoas'
        ]);
    }

    if(!Permissao::where('nome','=','pessoa_editar')->count()) {
        Permissao::create([
            'nome'=>'pessoa_editar',
            'descricao'=>'Editar Pessoas'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pessoa_editar')->first();
        $permissao->update([
            'nome'=>'pessoa_editar',
            'descricao'=>'Editar Pessoas'
        ]);
    }

    if(!Permissao::where('nome','=','pessoa_deletar')->count()) {
        Permissao::create([
            'nome'=>'pessoa_deletar',
            'descricao'=>'Deletar Pessoas'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','pessoa_deletar')->first();
        $permissao->update([
           'nome'=>'pessoa_deletar',
           'descricao'=>'Deletar Pessoas'
        ]);
    }

    if(!Permissao::where('nome','=','cidade_listar')->count()) {
        Permissao::create([
            'nome'=>'cidade_listar',
            'descricao'=>'Listar Cidades'
        ]);
    }else{
    $permissao = Permissao::where('nome','=','cidade_listar')->first();
    $permissao->update([
        'nome'=>'cidade_listar',
        'descricao'=>'Listar Cidades'
    ]);
    }

    if(!Permissao::where('nome','=','cidades_adicionar')->count()) {
        Permissao::create([
            'nome'=>'cidade_adicionar',
            'descricao'=>'Adicionar Cidades'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','cidade_adicionar')->first();
        $permissao->update([
            'nome'=>'cidade_adicionar',
            'descricao'=>'Adicionar Cidades'
        ]);
    }

    if(!Permissao::where('nome','=','cidade_editar')->count()) {
        Permissao::create([
            'nome'=>'cidade_editar',
            'descricao'=>'Editar Cidades'
        ]);
    }else{
    $permissao = Permissao::where('nome','=','cidade_editar')->first();
        $permissao->update([
            'nome'=>'cidade_editar',
            'descricao'=>'Editar Cidades'
        ]);
    }

    if(!Permissao::where('nome','=','cidade_deletar')->count()) {
        Permissao::create([
            'nome'=>'cidade_deletar',
            'descricao'=>'Deletar Cidades'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','cidade_deletar')->first();
        $permissao->update([
        'nome'=>'cidade_deletar',
        'descricao'=>'Deletar Cidades'
        ]);
    }

    if(!Permissao::where('nome','=','relato_listar')->count()) {
        Permissao::create([
            'nome'=>'relato_listar',
            'descricao'=>'Listar Relato'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','relato_listar')->first();
        $permissao->update([
            'nome'=>'relato_listar',
            'descricao'=>'Listar Relato'
        ]);
    }

    if(!Permissao::where('nome','=','relato_adicionar')->count()) {
        Permissao::create([
            'nome'=>'relato_adicionar',
            'descricao'=>'Adicionar Relato'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','relato_adicionar')->first();
        $permissao->update([
            'nome'=>'relato_adicionar',
            'descricao'=>'Adicionar Relato'
        ]);
    }

    if(!Permissao::where('nome','=','relato_editar')->count()) {
        Permissao::create([
            'nome'=>'relato_editar',
            'descricao'=>'Editar Relato'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','relato_editar')->first();
        $permissao->update([
            'nome'=>'relato_editar',
            'descricao'=>'Editar Relato'
        ]);
    }

    if(!Permissao::where('nome','=','relato_deletar')->count()) {
        Permissao::create([
            'nome'=>'relato_deletar',
            'descricao'=>'Excluir Relato'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','relato_deletar')->first();
        $permissao->update([
            'nome'=>'relato_deletar',
            'descricao'=>'Excluir Relato'
        ]);
    }

    if(! Permissao::where('nome','=','importar_bdSNCI')->count()) {
        Permissao::create([
            'nome'=>'importar_bdSNCI',
            'descricao'=>'Importar Bases SNCI'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_bdSNCI')->first();
        $permissao->update([
            'nome'=>'importar_bdSNCI',
            'descricao'=>'Importar Bases SNCI'
        ]);
    }

    if(! Permissao::where('nome','=','inspecao_gerar_xml')->count()) {
        Permissao::create([
            'nome'=>'inspecao_gerar_xml',
            'descricao'=>'Gerar XML'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','inspecao_gerar_xml')->first();
        $permissao->update([
            'nome'=>'inspecao_gerar_xml',
            'descricao'=>'Gerar XML'
        ]);
    }



    if(! Permissao::where('nome','=','inspecao_recusar')->count()) {
        Permissao::create([
            'nome'=>'inspecao_recusar',
            'descricao'=>'Recusar'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','inspecao_recusar')->first();
        $permissao->update([
            'nome'=>'inspecao_recusar',
            'descricao'=>'Recusar'
        ]);
    }

    if(! Permissao::where('nome','=','tipoUnidade_adicionar')->count()) {
        Permissao::create([
            'nome'=>'tipoUnidade_adicionar',
            'descricao'=>'Adicionar Tipo de Unidades'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','tipoUnidade_adicionar')->first();
        $permissao->update([
            'nome'=>'tipoUnidade_adicionar',
            'descricao'=>'Adicionar Tipo de Unidades'
        ]);
    }

    if(! Permissao::where('nome','=','tipoUnidade_deletar')->count()) {
        Permissao::create([
            'nome'=>'tipoUnidade_deletar',
            'descricao'=>'Deletar Tipo de Unidades'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','tipoUnidade_deletar')->first();
        $permissao->update([
            'nome'=>'tipoUnidade_deletar',
            'descricao'=>'Deletar Tipo de Unidades'
        ]);
    }


    if(! Permissao::where('nome','=','tipoUnidade_editar')->count()) {
        Permissao::create([
            'nome'=>'tipoUnidade_editar',
            'descricao'=>'Editar Tipo de Unidades'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','tipoUnidade_editar')->first();
        $permissao->update([
            'nome'=>'tipoUnidade_editar',
            'descricao'=>'Editar Tipo de Unidades'
        ]);
    }

    if(! Permissao::where('nome','=','tipoUnidade_listar')->count()) {
        Permissao::create([
            'nome'=>'tipoUnidade_listar',
            'descricao'=>'Listar Tipo de Unidades'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','tipoUnidade_listar')->first();
        $permissao->update([
            'nome'=>'tipoUnidade_listar',
            'descricao'=>'Listar Tipo de Unidades'
        ]);
    }


    if(! Permissao::where('nome','=','unidade_atualizar')->count()) {
        Permissao::create([
            'nome'=>'unidade_atualizar',
            'descricao'=>'Atualizar Unidades'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','unidade_atualizar')->first();
        $permissao->update([
            'nome'=>'unidade_atualizar',
            'descricao'=>'Atualizar Unidades'
        ]);
    }

    if(! Permissao::where('nome','=','importar_alarme')->count()) {
        Permissao::create([
            'nome'=>'importar_alarme',
            'descricao'=>'Importar Alarme'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_alarme')->first();
        $permissao->update([
            'nome'=>'importar_alarme',
            'descricao'=>'Importar Alarme'
        ]);
    }


    //    importar_bdfFat_02	Importar BDF FAT-02
    if(! Permissao::where('nome','=','importar_bdfFat_02')->count()) {
        Permissao::create([
            'nome'=>'importar_bdfFat_02',
            'descricao'=>'Importar BDF FAT-02'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_bdfFat_02')->first();
        $permissao->update([
            'nome'=>'importar_bdfFat_02',
            'descricao'=>'Importar BDF FAT-02'
        ]);
    }

    //    importar_bdSNCI	Importar Bases SNCI
    if(! Permissao::where('nome','=','importar_bdSNCI')->count()) {
        Permissao::create([
            'nome'=>'importar_bdSNCI',
            'descricao'=>'Importar Bases SNCI'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_bdSNCI')->first();
        $permissao->update([
            'nome'=>'importar_bdSNCI',
            'descricao'=>'Importar Bases SNCI'
        ]);
    }

    //    importar_cadastral	Importar Cadastral
    if(! Permissao::where('nome','=','importar_cadastral')->count()) {
        Permissao::create([
            'nome'=>'importar_cadastral',
            'descricao'=>'Importar Cadastral'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_cadastral')->first();
        $permissao->update([
            'nome'=>'importar_cadastral',
            'descricao'=>'Importar Cadastral'
        ]);
    }

    //    importar_cftv	Importar CFTV
    if(! Permissao::where('nome','=','importar_cftv')->count()) {
        Permissao::create([
            'nome'=>'importar_cftv',
            'descricao'=>'Importar CFTV'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_cftv')->first();
        $permissao->update([
            'nome'=>'importar_cftv',
            'descricao'=>'Importar CFTV'
        ]);
    }

    //    importar_cieEletronica	Importar CIEs Eletrônicas
    if(! Permissao::where('nome','=','importar_cieEletronica')->count()) {
        Permissao::create([
            'nome'=>'importar_cieEletronica',
            'descricao'=>'Importar CIEs Eletrônicas'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_cieEletronica')->first();
        $permissao->update([
            'nome'=>'importar_cieEletronica',
            'descricao'=>'Importar CIEs Eletrônicas'
        ]);
    }

    //    importar_controleViagem	Importar Controle de Viagem
    if(! Permissao::where('nome','=','importar_controleViagem')->count()) {
        Permissao::create([
            'nome'=>'importar_controleViagem',
            'descricao'=>'Importar Controle de Viagem'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_controleViagem')->first();
        $permissao->update([
            'nome'=>'importar_controleViagem',
            'descricao'=>'Importar Controle de Viagem'
        ]);
    }

    //    importar_feriados	Importar Feriados
    if(! Permissao::where('nome','=','importar_feriados')->count()) {
        Permissao::create([
            'nome'=>'importar_feriados',
            'descricao'=>'Importar Feriados'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_feriados')->first();
        $permissao->update([
            'nome'=>'importar_feriados',
            'descricao'=>'Importar Feriados'
        ]);
    }

    //    importar_feriasPorMcu	importar Férias por MCU
    if(! Permissao::where('nome','=','importar_feriasPorMcu')->count()) {
        Permissao::create([
            'nome'=>'importar_feriasPorMcu',
            'descricao'=>'Importar Férias por MCU'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_feriasPorMcu')->first();
        $permissao->update([
            'nome'=>'importar_feriasPorMcu',
            'descricao'=>'Importar Férias por MCU'
        ]);
    }

    //    importar_frequenciaPorSE	Importar Frequência por SE
    if(! Permissao::where('nome','=','importar_frequenciaPorSE')->count()) {
        Permissao::create([
            'nome'=>'importar_frequenciaPorSE',
            'descricao'=>'Importar Frequência por SE'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_frequenciaPorSE')->first();
        $permissao->update([
            'nome'=>'importar_frequenciaPorSE',
            'descricao'=>'Importar Frequência por SE'
        ]);
    }

    //    importar_microStrategy	Importar MicroStrategy
    if(! Permissao::where('nome','=','importar_microStrategy')->count()) {
        Permissao::create([
            'nome'=>'importar_microStrategy',
            'descricao'=>'Importar MicroStrategy'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_microStrategy')->first();
        $permissao->update([
            'nome'=>'importar_microStrategy',
            'descricao'=>'Importar MicroStrategy'
        ]);
    }

    //    importar_pagamentosAdicionais	Importar Pagamentos Adicionais
    if(! Permissao::where('nome','=','importar_pagamentosAdicionais')->count()) {
        Permissao::create([
            'nome'=>'importar_pagamentosAdicionais',
            'descricao'=>'Importar Pagamentos Adicionais'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_pagamentosAdicionais')->first();
        $permissao->update([
            'nome'=>'importar_pagamentosAdicionais',
            'descricao'=>'Importar Pagamentos Adicionais'
        ]);
    }

    //    importar_painelExtravio	Importar Painel de Extravio
    if(! Permissao::where('nome','=','importar_painelExtravio')->count()) {
        Permissao::create([
            'nome'=>'importar_painelExtravio',
            'descricao'=>'Importar  Painel de Extravio'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_painelExtravio')->first();
        $permissao->update([
            'nome'=>'importar_painelExtravio',
            'descricao'=>'Importar  Painel de Extravio'
        ]);
    }

    //    importar_PLPs	Importar PLPs Pendente
    if(! Permissao::where('nome','=','importar_PLPs')->count()) {
        Permissao::create([
            'nome'=>'importar_PLPs',
            'descricao'=>'Importar PLPs Pendente'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_PLPs')->first();
        $permissao->update([
            'nome'=>'importar_PLPs',
            'descricao'=>'Importar PLPs Pendente'
        ]);
    }

    //    importar_proter	Importar Proter
    if(! Permissao::where('nome','=','importar_proter')->count()) {
        Permissao::create([
            'nome'=>'importar_proter',
            'descricao'=>'Importar Proter'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_proter')->first();
        $permissao->update([
            'nome'=>'importar_proter',
            'descricao'=>'Importar Proter'
        ]);
    }


    //    importar_respDefinida	Importar Responsabilidade Definida
    if(! Permissao::where('nome','=','importar_respDefinida')->count()) {
        Permissao::create([
            'nome'=>'importar_respDefinida',
            'descricao'=>'Importar Responsabilidade Definida'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_respDefinida')->first();
        $permissao->update([
            'nome'=>'importar_respDefinida',
            'descricao'=>'Importar Responsabilidade Definida'
        ]);
    }

    //    importar_sgdo	Importar SGDO
    if(! Permissao::where('nome','=','importar_sgdo')->count()) {
        Permissao::create([
            'nome'=>'importar_sgdo',
            'descricao'=>'Importar SGDO'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_sgdo')->first();
        $permissao->update([
            'nome'=>'importar_sgdo',
            'descricao'=>'Importar SGDO'
        ]);
    }

    //    importar_sld02Bdf	Importar SL02 BDF
    if(! Permissao::where('nome','=','importar_sld02Bdf')->count()) {
        Permissao::create([
            'nome'=>'importar_sld02Bdf',
            'descricao'=>'Importar SL02 BDF'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_sld02Bdf')->first();
        $permissao->update([
            'nome'=>'importar_sld02Bdf',
            'descricao'=>'Importar SL02 BDF'
        ]);
    }

    //    importar_smbBdf	Importar SMBxBDF
    if(! Permissao::where('nome','=','importar_smbBdf')->count()) {
        Permissao::create([
            'nome'=>'importar_smbBdf',
            'descricao'=>'Importar SMBxBDF'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_smbBdf')->first();
        $permissao->update([
            'nome'=>'importar_smbBdf',
            'descricao'=>'Importar SMBxBDF'
        ]);
    }

    //    importar_unidades	Importar Unidades Operacionais
    if(! Permissao::where('nome','=','importar_unidades')->count()) {
        Permissao::create([
            'nome'=>'importar_unidades',
            'descricao'=>'Importar Unidades Operacionais'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_unidades')->first();
        $permissao->update([
            'nome'=>'importar_unidades',
            'descricao'=>'Importar Unidades Operacionais'
        ]);
    }

    //    importar_webCont	Importar WebCont
    if(! Permissao::where('nome','=','importar_webCont')->count()) {
        Permissao::create([
            'nome'=>'importar_webCont',
            'descricao'=>'Importar WebCont'
        ]);
    }else{
        $permissao = Permissao::where('nome','=','importar_webCont')->first();
        $permissao->update([
            'nome'=>'importar_webCont',
            'descricao'=>'Importar WebCont'
        ]);
    }
       echo "Permissões geradas com sucesso!\n";
    }
}
