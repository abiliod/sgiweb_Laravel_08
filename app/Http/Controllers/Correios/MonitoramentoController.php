<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use App\Jobs\AvaliaInspecao;
use App\Jobs\GeraInspecao;
use App\Jobs\JobXml_Inspecao;
use App\Models\Correios\Inspecao;
use App\Models\Correios\Itensdeinspecao;
use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use App\Models\Correios\ModelsDto\AcessoFinalSemana;
use App\Models\Correios\ModelsDto\CompartilhaSenha;
use App\Models\Correios\SequenceInspecao;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\PhpOffice\PhpSpreadsheet\Shared\Date;
use Auth;


class MonitoramentoController extends Controller
{

    public function avaliacao(Request $request) {
        $dtnow = new Carbon();
        $dtmenos30dias = new Carbon();
        $dtmenos60dias = new Carbon();
        $dtmenos90dias = new Carbon();
        $dtmenos3meses = new Carbon();
        $dtmenos110dias = new Carbon();
        $dtmenos120dias = new Carbon();
        $dtmenos150dias = new Carbon();
        $dtmenos4meses = new Carbon();
        $dtmenos6meses = new Carbon();
        $dtmenos12meses = new Carbon();
        $dtmenos30dias->subDays(30);
        $dtmenos60dias->subDays(60);
        $dtmenos90dias->subDays(90);
        $dtmenos3meses->subMonth(3);
        $dtmenos110dias->subDays(110);
        $dtmenos120dias->subDays(120);
        $dtmenos150dias->subDays(150);
        $dtmenos4meses->subMonth(4);
        $dtmenos6meses->subMonth(6);
        $dtmenos12meses->subMonth(12);

        $codVerificacaoAnterior = null;
        $numeroGrupoReincidente = null;
        $numeroItemReincidente = null;
        $validator = Validator::make($request->all(), [
            'superintendencia' => 'required'
            , 'tipodeunidade' => 'required'
        ]);
        if (!$validator->passes()) {
            \Session::flash('mensagem', ['msg' => 'Parâmetros insuficiente para o agendamento do Job.'
                , 'class' => 'red white-text']);
            return redirect()->back();
        }
        else {
            $superintendencias = $request->all(['superintendencia']);
            $tipodeunidade = $request->all(['tipodeunidade']);
            $ciclo = $request->all(['ciclo']);
//#########################################################################################################
//            para ativar a fila no console
//            php artisan queue:work --queue=avaliaInspecao

//

            $job = (new AvaliaInspecao($superintendencias, $tipodeunidade , $ciclo))
                ->onQueue('avaliaInspecao')->delay($dtnow->addMinutes(1));
            dispatch($job);

            \Session::flash('mensagem', ['msg' => 'Job AvaliaInspecao aguardando processamento.'
                , 'class' => 'blue white-text']);
            return redirect()->back();

//   O valor de 134217728 bytes é equivalente a 128M

//   Erro de Memória.Isso ocorre porque no arquivo php.ini o parâmetro memory_limit está configurado para 128M. E para consertar o problema é só editar o arquivo e alterar para 256M.
//#########################################################################################################

            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            foreach ($superintendencias as $res) { // request é Array de indice para Superintendências
                foreach ($res as $superintendencia) { // percorre o Array de objeto Superintendências

                    // testa se o primeiro parâmetro é para todas superintendecia onde SE == 1
                    // Inicio do teste para todas superintendencias
                    if ($superintendencia == 1) {
                        // se verdadeiro se SE == 1 seleciona todas superintendência cujo a SE > 1
                        $registros = DB::table('itensdeinspecoes')
                            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                            ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
                            ->where([['situacao', '=', 'Em Inspeção']])
                            ->where([['se', '=', 1]])
                            ->where([['inspecoes.ciclo', '=', $ciclo]])
                            ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
//                            ->where([['sto', '=', 16301561 ]]) // ACREUNA
                            //->limit(100)
                            ->get();

//                      Inicio processamento da aavaliação
                        foreach ($registros as $registro) {
                            $consequencias = $registro->consequencias;
                            $orientacao = $registro->orientacao;

//   no Job coloque aqui todos os testes  ex:
//
                        }

                    }
                    // Fim do teste para todas superintendencias se superintendencia = 1

                    // inicio do teste para uma superintendencias
                    else {


                        $registros = DB::table('itensdeinspecoes')
                            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                            ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
                            ->where([['situacao', '=', 'Em Inspeção']])
                            ->where([['se', '=', $superintendencia]])
                            ->where([['inspecoes.ciclo', '=', $ciclo]])
                            ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
//                            ->where([['sto', '=', 16300882 ]]) // Anicuns
//                            ->where([['sto', '=', 16303407 ]]) // alto horizonte
//                            ->where([['sto', '=', 16300947 ]])   // Britania  16300947
//                            ->where([['mcu', '=', 6684 ]]) //ac anicuns
//                            ->where([['itensdeinspecoes.testeVerificacao_id', '=', 3708 ]])  //3678  é smb_bdf
//                            ->limit(100)

                        ->get();



//                      Inicio processamento da aavaliação
                        foreach ($registros as $registro) {
                            $consequencias = $registro->consequencias;
                            $orientacao = $registro->orientacao;


                            ini_set('memory_limit', '512M');
                            ini_set('max_input_time', 350);
                            ini_set('max_execution_time', 350);

// Inicio Pre Alerta gestão automatica unidade sem supervisor
                            if((($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==7))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==6))) {

                                $codVerificacaoAnterior = null;
                                $numeroGrupoReincidente = null;
                                $numeroItemReincidente = null;
                                $evidencia = null;
                                $valorSobra = null;
                                $valorFalta = null;
                                $valorRisco = null;
                                $total = 0;
                                $pontuado = null;
                                $aviso = null;
                                $itemQuantificado = 'Não';
                                $reincidente = 0;
                                $reinc = 'Não';

                                $dtini = $dtmenos150dias;
                                $countSupervisor = 0;
                                $count = 0;

                                switch ($registro->se) {

                                    case 1 :{ $superintendência = 'CS'; } break;
                                    case 4 :{ $superintendência = 'AL'; } break;
                                    case 6 :{ $superintendência = 'AM'; } break;
                                    case 8 :{ $superintendência = 'BA'; } break;
                                    case 10 :{ $superintendência = 'BSB'; } break;
                                    case 12 :{ $superintendência = 'CE'; } break;
                                    case 14 :{ $superintendência = 'ES'; } break;
                                    case 16 :{ $superintendência = 'GO'; } break;
                                    case 18 :{ $superintendência = 'MA'; } break;
                                    case 20 :{ $superintendência = 'MG'; } break;
                                    case 22 :{ $superintendência = 'MS'; } break;
                                    case 24 :{ $superintendência = 'MT'; } break;
                                    case 26 :{ $superintendência = 'RO'; } break;
                                    case 28 :{ $superintendência = 'PA'; } break;
                                    case 30 :{ $superintendência = 'PB'; } break;
                                    case 32 :{ $superintendência = 'PE'; } break;
                                    case 34 :{ $superintendência = 'PI'; } break;
                                    case 36 :{ $superintendência = 'PR'; } break;
                                    case 50 :{ $superintendência = 'RJ'; } break;
                                    case 60 :{ $superintendência = 'RN'; } break;
                                    case 64 :{ $superintendência = 'RS'; } break;
                                    case 68 :{ $superintendência = 'SC'; } break;
                                    case 72 :{ $superintendência = 'SPM'; } break;
                                    case 74 :{ $superintendência = 'SPI'; } break;
                                    case 75 :{ $superintendência = 'TO'; } break;
                                }

                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%quantidade recebida no SGDO%']])
                                    ->where([['sto', '=', $registro->sto]])
                                    ->orderBy('no_inspecao', 'desc')
                                    ->first();

                                try {

                                    if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                                        $reincidente = 1;
                                        $reinc = 'Sim';
                                        $periodo = new CarbonPeriod();
                                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                                        $numeroItemReincidente = $reincidencia->no_item;
                                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                                        $painel_extravios = DB::table('painel_extravios')
                                            ->select( 'painel_extravios.*' )
                                            ->where([['painel_extravios.data_evento', '>=',  $reincidencia_dt_fim_inspecao  ]])
                                            ->where([['painel_extravios.dr_destino', '=',  $superintendência  ]])//o relatório não tem mcu
                                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                                            ->get();
                                        $dtini = $reincidencia_dt_fim_inspecao;

                                    } else {
                                        $painel_extravios = DB::table('painel_extravios')
                                            ->select( 'painel_extravios.*' )
                                            ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                                            ->where([['painel_extravios.dr_destino', '=',  $superintendência  ]])//o relatório não tem mcu
                                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                                            ->get();
                                    }
                                } catch (\Exception $e) {

                                    $painel_extravios = DB::table('painel_extravios')
                                        ->select( 'painel_extravios.*' )
                                        ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                                        ->where([['painel_extravios.dr_destino', '=',  $superintendência  ]])//o relatório não tem mcu
                                        ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                                        ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                                        ->get();
                                }



                                $count = $painel_extravios->count('unid_destino_apelido');
                                $dtfim = $painel_extravios->max('data_evento');
                                $cadastral = DB::table('cadastral')
                                    ->select( 'cadastral.*' )
                                    ->where([['cadastral.mcu', '=',   $registro->mcu  ]])
                                    ->where('cadastral.funcao',  'like', '%' . 'SUPERVISOR' . '%')
                                    ->get();
                                $countSupervisor = $cadastral->count('funcao');

                                if($countSupervisor >= 1){
                                    $avaliacao = 'Não Verificado';
                                    $oportunidadeAprimoramento = 'Unidade avaliada em outro item Pois Possui Supervisor.';
                                    $consequencias = null;
                                    $orientacao = null;

                                }
                                else{
                                    if ($count >= 1){
                                         if(! $painel_extravios->isEmpty()){
                                             $avaliacao = 'Não Conforme';
                                             $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna "gesto pré-alerta" a ocorrência de "Gestão Automática" para {{$count}} objeto(s), indicando que não era realizada a "gestão diária" do Pré-Alerta na unidade, conforme relatado a seguir:';

                                             $evidencia = $evidencia
                                                 . "\n" . 'Objeto'
                                                 . "\t" . 'Data Último Evento';

                                             foreach($painel_extravios as $dados){
                                                 $ultimoEvento = $dados->ultimo_evento_data == '' ? 'Data não Registrada' : date('d/m/Y', strtotime($dados->ultimo_evento_data));
                                                 $evidencia = $evidencia
                                                 . "\n" . $dados->objeto
                                                 . "\t" . $ultimoEvento;
                                             }
                                             $consequencias = $registro->consequencias;
                                             $orientacao = $registro->orientacao;
                                        }

                                        $quebra = DB::table('relevancias')
                                            ->select('valor_final')
                                            ->where('fator_multiplicador', '=', 1)
                                            ->first();
                                        $quebracaixa = $quebra->valor_final * 0.1;

                                        if( $valorFalta > $quebracaixa){
                                            $fm = DB::table('relevancias')
                                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                                ->where('valor_inicio', '<=', $total)
                                                ->orderBy('valor_final', 'desc')
                                                ->first();
                                            $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                        }
                                        else{
                                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                                        }
                                    }
                                    else{
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna Gestão Pré-alerta que não havia ocorrência alusiva à Gestão Automática que sugerisse falha na Gestão do diária da Conferência Eletrônica da unidade inspecionada.';
                                        $consequencias = null;
                                        $orientacao =  null;
                                    }
                                }

                                $dto = DB::table('itensdeinspecoes')
                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                    ->select('itensdeinspecoes.*')
                                    ->first();

                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                $itensdeinspecao->avaliacao = $avaliacao;
                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                $itensdeinspecao->evidencia = $evidencia;
                                $itensdeinspecao->valorFalta = $valorFalta;
                                $itensdeinspecao->valorSobra = $valorSobra;
                                $itensdeinspecao->valorRisco = $valorRisco;
                                $itensdeinspecao->situacao = 'Inspecionado';
                                $itensdeinspecao->pontuado = $pontuado;
                                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                                $itensdeinspecao->orientacao = $registro->orientacao;
                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                $itensdeinspecao->reincidencia = $reinc;
                                $itensdeinspecao->consequencias = $consequencias;
                                $itensdeinspecao->orientacao = $orientacao;
                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;

//                                echo  "\n" .'avaliação ',$itensdeinspecao;

                                $itensdeinspecao->update();

                            }
// fim Pre Alerta gestão automatica unidade sem supervisor

// Inicio Pre Alerta gestão automatica unidade com supervisor

                            if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==15))
                                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==11))
                                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==8))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==5))) {


                                $codVerificacaoAnterior = null;
                                $numeroGrupoReincidente = null;
                                $numeroItemReincidente = null;
                                $evidencia = null;
                                $valorSobra = null;
                                $valorFalta = null;
                                $valorRisco = null;
                                $total = 0;
                                $pontuado = null;
                                $aviso = null;
                                $itemQuantificado = 'Não';
                                $reincidente = 0;
                                $reinc = 'Não';

                                $dtini = $dtmenos150dias;
                                $countSupervisor = 0;
                                $count = 0;

                                switch ($registro->se) {

                                    case 1 :{ $superintendência = 'CS'; } break;
                                    case 4 :{ $superintendência = 'AL'; } break;
                                    case 6 :{ $superintendência = 'AM'; } break;
                                    case 8 :{ $superintendência = 'BA'; } break;
                                    case 10 :{ $superintendência = 'BSB'; } break;
                                    case 12 :{ $superintendência = 'CE'; } break;
                                    case 14 :{ $superintendência = 'ES'; } break;
                                    case 16 :{ $superintendência = 'GO'; } break;
                                    case 18 :{ $superintendência = 'MA'; } break;
                                    case 20 :{ $superintendência = 'MG'; } break;
                                    case 22 :{ $superintendência = 'MS'; } break;
                                    case 24 :{ $superintendência = 'MT'; } break;
                                    case 26 :{ $superintendência = 'RO'; } break;
                                    case 28 :{ $superintendência = 'PA'; } break;
                                    case 30 :{ $superintendência = 'PB'; } break;
                                    case 32 :{ $superintendência = 'PE'; } break;
                                    case 34 :{ $superintendência = 'PI'; } break;
                                    case 36 :{ $superintendência = 'PR'; } break;
                                    case 50 :{ $superintendência = 'RJ'; } break;
                                    case 60 :{ $superintendência = 'RN'; } break;
                                    case 64 :{ $superintendência = 'RS'; } break;
                                    case 68 :{ $superintendência = 'SC'; } break;
                                    case 72 :{ $superintendência = 'SPM'; } break;
                                    case 74 :{ $superintendência = 'SPI'; } break;
                                    case 75 :{ $superintendência = 'TO'; } break;
                                }

                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%quantidade recebida no SGDO%']])
                                    ->where([['sto', '=', $registro->sto]])
                                    ->orderBy('no_inspecao', 'desc')
                                    ->first();

                                try {

                                    if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                                        $reincidente = 1;
                                        $reinc = 'Sim';
                                        $periodo = new CarbonPeriod();
                                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                                        $numeroItemReincidente = $reincidencia->no_item;
                                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                                        $painel_extravios = DB::table('painel_extravios')
                                            ->select( 'painel_extravios.*' )
                                            ->where([['painel_extravios.data_evento', '>=',  $reincidencia_dt_fim_inspecao  ]])
                                            ->where([['painel_extravios.dr_destino', '=',  $superintendência  ]])//o relatório não tem mcu
                                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                                            ->get();
                                        $dtini = $reincidencia_dt_fim_inspecao;

                                    } else {
                                        $painel_extravios = DB::table('painel_extravios')
                                            ->select( 'painel_extravios.*' )
                                            ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                                            ->where([['painel_extravios.dr_destino', '=',  $superintendência  ]])//o relatório não tem mcu
                                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                                            ->get();
                                    }
                                } catch (\Exception $e) {

                                    $painel_extravios = DB::table('painel_extravios')
                                        ->select( 'painel_extravios.*' )
                                        ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                                        ->where([['painel_extravios.dr_destino', '=',  $superintendência  ]])//o relatório não tem mcu
                                        ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                                        ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                                        ->get();
                                }



                                $count = $painel_extravios->count('unid_destino_apelido');
                                $dtfim = $painel_extravios->max('data_evento');
                                $cadastral = DB::table('cadastral')
                                    ->select( 'cadastral.*' )
                                    ->where([['cadastral.mcu', '=',   $registro->mcu  ]])
                                    ->where('cadastral.funcao',  'like', '%' . 'SUPERVISOR' . '%')
                                    ->get();
                                $countSupervisor = $cadastral->count('funcao');

                                if($countSupervisor == 0){
                                    $avaliacao = 'Não Verificado';
                                    $oportunidadeAprimoramento = 'Unidade avaliada em outro item dado a existênci de Supervisor no quadro de lotação.';
                                    $consequencias = null;
                                    $orientacao = null;

                                }
                                else{
                                    if ($count >= 1){
                                        if(! $painel_extravios->isEmpty()){
                                            $avaliacao = 'Não Conforme';
                                            $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna "gesto pré-alerta" a ocorrência de "Gestão Automática" para {{$count}} objeto(s), indicando que não era realizada a "gestão diária" do Pré-Alerta na unidade, conforme relatado a seguir:';

                                            $evidencia = $evidencia
                                                . "\n" . 'Objeto'
                                                . "\t" . 'Data Último Evento';

                                            foreach($painel_extravios as $dados){
                                                $ultimoEvento = $dados->ultimo_evento_data == '' ? 'Data não Registrada' : date('d/m/Y', strtotime($dados->ultimo_evento_data));
                                                $evidencia = $evidencia
                                                    . "\n" . $dados->objeto
                                                    . "\t" . $ultimoEvento;
                                            }
                                            $consequencias = $registro->consequencias;
                                            $orientacao = $registro->orientacao;
                                        }

                                        $quebra = DB::table('relevancias')
                                            ->select('valor_final')
                                            ->where('fator_multiplicador', '=', 1)
                                            ->first();
                                        $quebracaixa = $quebra->valor_final * 0.1;

                                        if( $valorFalta > $quebracaixa){
                                            $fm = DB::table('relevancias')
                                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                                ->where('valor_inicio', '<=', $total)
                                                ->orderBy('valor_final', 'desc')
                                                ->first();
                                            $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                        }
                                        else{
                                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                                        }
                                    }
                                    else{
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna Gestão Pré-alerta que não havia ocorrência alusiva à Gestão Automática que sugerisse falha na Gestão do diária da Conferência Eletrônica da unidade inspecionada.';
                                        $consequencias = null;
                                        $orientacao =  null;
                                    }
                                }

                                $dto = DB::table('itensdeinspecoes')
                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                    ->select('itensdeinspecoes.*')
                                    ->first();

                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                $itensdeinspecao->avaliacao = $avaliacao;
                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                $itensdeinspecao->evidencia = $evidencia;
                                $itensdeinspecao->valorFalta = $valorFalta;
                                $itensdeinspecao->valorSobra = $valorSobra;
                                $itensdeinspecao->valorRisco = $valorRisco;
                                $itensdeinspecao->situacao = 'Inspecionado';
                                $itensdeinspecao->pontuado = $pontuado;
                                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                                $itensdeinspecao->orientacao = $registro->orientacao;
                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                $itensdeinspecao->reincidencia = $reinc;
                                $itensdeinspecao->consequencias = $consequencias;
                                $itensdeinspecao->orientacao = $orientacao;
                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;

//                                echo  "\n" .'avaliação ',$itensdeinspecao;

                                $itensdeinspecao->update();


                            }
// fim Pre Alerta gestão automatica unidade com supervisor



//                                DB::enableQueryLog();


//                                dd( DB::getQueryLog());



                            ini_set('memory_limit', '128M');
                            ini_set('max_input_time', 120);
                            ini_set('max_execution_time', 120);


                        }

                    }
                    // Fim do teste para uma superintendencias
                }
            }
        }
    }

    public function avaliar() {
        $status = 'Criado e instalado';
        try {
            $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        }catch ( \Exception $e){
            return redirect()->route('login');
        }

        if (!empty($businessUnitUser)) {
            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id) {
                case 1:
                case 2:
                    {
                        $registros = DB::table('unidades')
                            ->select(
                                'id', 'se', 'seDescricao'
                            )
                            ->where([['se', '>', 1]])
                            ->where([['status_unidadeDesc', '=', $status]])
                            ->groupBy('se')
                            ->orderBy('seDescricao', 'asc')
                        ->get();

                    }
                    break;
            }

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
            ->get();

            return view('compliance.monitoramento.avaliar', compact('registros', 'tiposDeUnidade'));
        } else {
            \Session::flash('mensagem', ['msg' => 'Não foi possivel exibir os itens você provavelmente não é administrador.'
                , 'class' => 'red white-text']);
            return redirect()->route('home');
        }
    }

    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
             'data' => 'required'
            , 'superintendencia' => 'required'
            , 'tipodeunidade' => 'required'
            , 'ciclo' => 'required'
        ]);

        if (!$validator->passes()) {
            \Session::flash('mensagem', ['msg' => 'Parâmetros insuficiente para o agendamento do Job.'
                , 'class' => 'red white-text']);
            return redirect()->back();
        }
        else {
            $dados = $request->all();
            $superintendencias = $request->all(['superintendencia']);
            $ciclo = $dados['ciclo'];
            $dataAgenda = $this->transformDate($dados['data']);
            $status = 'Criado e instalado';
            $tipoInspecao = DB::table('tiposdeunidade')
                ->where([
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
                ->where([
                    ['id', '=', $dados['tipodeunidade']],
                ])
            ->get();


            if ($tipoInspecao->isEmpty()) {
                \Session::flash('mensagem', ['msg' => 'Tipo de Inspeção não Previsto!'
                    , 'class' => 'red white-text']);
                return redirect()->back();
            }
            else
            {


                // php artisan queue:work --queue=geraInspecao
                ini_set('memory_limit', '512M');
                ini_set('max_input_time', 350);
                ini_set('max_execution_time', 350);

  //                dd($superintendencias, $status , $ciclo, $dataAgenda);

                $job = (new GeraInspecao($superintendencias, $status , $ciclo, $dataAgenda))
                    ->onQueue('geraInspecao')->delay($dataAgenda->addMinutes(1));
                dispatch($job);

                \Session::flash('mensagem', ['msg' => 'Job aguardando processamento.'
                    , 'class' => 'blue white-text']);
                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->back();



            // ####################  begin  JOB ################
                foreach ($superintendencias as $dados) {
                    foreach ($dados as $superintendencia) {
                        if ($superintendencia == 1)
                        {
                            $unidades = DB::table('unidades')
                                ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                                ->select(
                                    'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                                )
                                ->where([['tiposdeunidade.inspecionar', '=', 'Sim']])
                                ->where([['tiposdeunidade.tipoInspecao', '=', 'Monitorada']])
                                ->where([['unidades.status_unidadeDesc', '=', $status]])
                                ->Where([['se', '>', $superintendencia]])
                                ->orderBy('seDescricao', 'desc')
                                ->orderBy('tipoOrgaoDesc', 'asc')
                                ->orderBy('descricao', 'asc')
                                ->get();
                            if (!$unidades->isEmpty()) {
                                foreach ($unidades as $unidade) {

                                    //checa se já não existe inspeção para o ciclo
                                    $res = DB::table('inspecoes')
                                        ->Where([['unidade_id', '=', $unidade->id]])
                                        ->Where([['ciclo', '=', $ciclo ]])
                                        ->Where([['tipoVerificacao', '=', 'Monitorada' ]])
                                        ->get();
                                    if ($res->isEmpty()){
//                              gera numeração
                                        $sequence_inspcaos = DB::table('sequence_inspcaos')
                                            ->select('sequence_inspcaos.*')
                                            ->Where([['se', '=', $superintendencia]])
                                            ->Where([['ciclo', '=', $ciclo ]])
                                            ->first();
                                        $strsuperintendencia = intval($superintendencia);
                                        if (!empty($sequence_inspcaos)) {
                                            $sequence_id = $sequence_inspcaos->id;
                                            $sequence = $sequence_inspcaos->sequence;
                                            $sequence ++;
                                            $affected = DB::table('sequence_inspcaos')
                                                ->where('id', $sequence_id)
                                                ->update(['sequence' => $sequence]);
                                            //      dd($affected , $sequence);
                                        } else {
                                            $sequence = 2001;
                                            $affected =  SequenceInspecao::create([
                                                'se' => $strsuperintendencia
                                                ,'ciclo' => $ciclo
                                                ,'sequence' => $sequence
                                            ]);
//                                           dd('else ' ,$affected);
                                        }

//                                        dd($superintendencia, $strsuperintendencia, $sequence_inspcaos);
                                        $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
                                        if ($strsuperintendencia < 10) {
                                            $se = '0'.$strsuperintendencia;
                                        } else {
                                            $se = $strsuperintendencia;
                                        }
                                        $codigo = $se . $sequence . $ciclo;
                                        // dd($codigo);
                                        $inspecao = new Inspecao;
                                        $inspecao->ciclo = $ciclo;
                                        $inspecao->descricao = $unidade->descricao;
                                        $inspecao->datainiPreInspeção = $dataAgenda ;
                                        $inspecao->codigo = $codigo;
                                        $inspecao->unidade_id = $unidade->id;
                                        $inspecao->tipoUnidade_id = $unidade->tipoUnidade_id;
                                        $inspecao->tipoVerificacao = 'Monitorada';
                                        $inspecao->status =  'Em Inspeção';
                                        $inspecao->unidade_id = $unidade->id;
                                        $inspecao->save();
                                        $parametros = DB::table('tiposdeunidade')
                                            ->join('gruposdeverificacao', 'tiposdeunidade.id', '=', 'tipoUnidade_id')
                                            ->join('testesdeverificacao', 'grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                                            ->where([
                                                ['gruposdeverificacao.tipoUnidade_id', '=', $inspecao->tipoUnidade_id] //" tipoUnidade_id " => " 1 "
                                            ])
                                            ->where([
                                                ['gruposdeverificacao.tipoVerificacao', '=', $inspecao->tipoVerificacao] //" tipoVerificacao " => " Remoto "
                                            ])
                                            ->where([
                                                ['gruposdeverificacao.ciclo', '=', $inspecao->ciclo] // REGRA o Caderno é por ciclo
                                            ])
                                            ->get();
                                        foreach ($parametros as $parametro) {
                                            $registro = new Itensdeinspecao;
                                            $registro->inspecao_id = $inspecao->id; //veriricação relacionada
                                            $registro->unidade_id = $unidade->id; //unidade verificada
                                            $registro->tipoUnidade_id = $unidade->tipoUnidade_id; //Tipo de unidade
                                            $registro->grupoVerificacao_id = $parametro->grupoVerificacao_id;//grupo de verificação
                                            $registro->testeVerificacao_id = $parametro->id;// $registro->id teste de verificação
                                            $registro->oportunidadeAprimoramento = $parametro->roteiroConforme;
                                            $registro->consequencias = $parametro->consequencias;
                                            $registro->norma = $parametro->norma;
                                            $registro->save();
                                        }
                                    }
                                    //    dd($inspecao ,'ultimo teste ->',$registro);
                                }
                            }
                        }
                        else
                        {
                            //gerar para todas selecionadas
                            $unidades = DB::table('unidades')
                                ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                                ->select(
                                    'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                                )
                                ->where([['tiposdeunidade.inspecionar', '=', 'Sim']])
                                ->where([['tiposdeunidade.tipoInspecao', '=', 'Monitorada']])
                                ->where([['unidades.status_unidadeDesc', '=', $status]])
                                ->Where([['se', '=', $superintendencia]])
                                ->orderBy('seDescricao', 'desc')
                                ->orderBy('tipoOrgaoDesc', 'asc')
                                ->orderBy('descricao', 'asc')
                                ->get();
                            if (!$unidades->isEmpty())
                            {
                                foreach ($unidades as $unidade) {

                                    //checa se já não existe inspeção para o ciclo
                                    $res = DB::table('inspecoes')
                                        ->Where([['unidade_id', '=', $unidade->id]])
                                        ->Where([['ciclo', '=', $ciclo ]])
                                        ->Where([['tipoVerificacao', '=', 'Monitorada' ]])
                                        ->get();
                                    if ($res->isEmpty()){
//                              gera numeração
                                        $sequence_inspcaos = DB::table('sequence_inspcaos')
                                            ->select('sequence_inspcaos.*')
                                            ->Where([['se', '=', $superintendencia]])
                                            ->Where([['ciclo', '=', $ciclo ]])
                                        ->first();
                                        $strsuperintendencia = intval($superintendencia);
                                        if (!empty($sequence_inspcaos)) {
                                            $sequence_id = $sequence_inspcaos->id;
                                            $sequence = $sequence_inspcaos->sequence;
                                            $sequence ++;
                                            $affected = DB::table('sequence_inspcaos')
                                                ->where('id', $sequence_id)
                                                ->update(['sequence' => $sequence]);
                                      //      dd($affected , $sequence);
                                        } else {
                                            $sequence = 2001;
                                            $affected =  SequenceInspecao::create([
                                                'se' => $strsuperintendencia
                                                ,'ciclo' => $ciclo
                                                ,'sequence' => $sequence
                                            ]);
//                                           dd('else ' ,$affected);
                                        }

//                                        dd($superintendencia, $strsuperintendencia, $sequence_inspcaos);
                                        $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
                                        if ($strsuperintendencia < 10) {
                                            $se = '0'.$strsuperintendencia;
                                        } else {
                                            $se = $strsuperintendencia;
                                        }
                                        $codigo = $se . $sequence . $ciclo;
                                       // dd($codigo);
                                        $inspecao = new Inspecao;
                                        $inspecao->ciclo = $ciclo;
                                        $inspecao->descricao = $unidade->descricao;
                                        $inspecao->datainiPreInspeção = $dataAgenda ;
                                        $inspecao->codigo = $codigo;
                                        $inspecao->unidade_id = $unidade->id;
                                        $inspecao->tipoUnidade_id = $unidade->tipoUnidade_id;
                                        $inspecao->tipoVerificacao = 'Monitorada';
                                        $inspecao->status =  'Em Inspeção';
                                        $inspecao->unidade_id = $unidade->id;
                                        $inspecao->save();
                                        $parametros = DB::table('tiposdeunidade')
                                            ->join('gruposdeverificacao', 'tiposdeunidade.id', '=', 'tipoUnidade_id')
                                            ->join('testesdeverificacao', 'grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                                            ->where([
                                                ['gruposdeverificacao.tipoUnidade_id', '=', $inspecao->tipoUnidade_id] //" tipoUnidade_id " => " 1 "
                                            ])
                                            ->where([
                                                ['gruposdeverificacao.tipoVerificacao', '=', $inspecao->tipoVerificacao] //" tipoVerificacao " => " Remoto "
                                            ])
                                            ->where([
                                                ['gruposdeverificacao.ciclo', '=', $inspecao->ciclo] // REGRA o Caderno é por ciclo
                                            ])
                                            ->get();
                                        foreach ($parametros as $parametro) {
                                            $registro = new Itensdeinspecao;
                                            $registro->inspecao_id = $inspecao->id; //veriricação relacionada
                                            $registro->unidade_id = $unidade->id; //unidade verificada
                                            $registro->tipoUnidade_id = $unidade->tipoUnidade_id; //Tipo de unidade
                                            $registro->grupoVerificacao_id = $parametro->grupoVerificacao_id;//grupo de verificação
                                            $registro->testeVerificacao_id = $parametro->id;// $registro->id teste de verificação
                                            $registro->oportunidadeAprimoramento = $parametro->roteiroConforme;
                                            $registro->consequencias = $parametro->consequencias;
                                            $registro->norma = $parametro->norma;
                                            $registro->save();
                                        }
                                    }
                                //    dd($inspecao ,'ultimo teste ->',$registro);
                                }
                            }
                        }
                    }
                }
            //  ####################  end  JOB ################

            }
        }

    }

    public function criar() {
        $status = 'Criado e instalado';
        try {
            $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        }catch ( \Exception $e){
        return redirect()->route('login');
    }


        if (!empty($businessUnitUser)) {
            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id) {
                case 1:
                case 2:
                    {
                        $registros = DB::table('unidades')
                            ->select(
                                'id', 'se', 'seDescricao'
                            )
                            ->where([['se', '>', 1]])
                            ->where([['status_unidadeDesc', '=', $status]])
                            ->groupBy('se')
                            ->orderBy('seDescricao', 'asc')
                        ->get();

                    }
                    break;
            }

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
            ->get();

            return view('compliance.monitoramento.criar', compact('registros', 'tiposDeUnidade'));
        } else {
            \Session::flash('mensagem', ['msg' => 'Não foi possivel exibir os itens você provavelmente não é administrador.'
                , 'class' => 'red white-text']);
            return redirect()->route('home');
        }
    }

    public function show(){
        return view('compliance.monitoramento.show');  //
    }

    public function gerar_xml(Request $request)
    {
        $dtnow = new Carbon();
        $validator = Validator::make($request->all(), [
            'superintendencia' => 'required'
            , 'tipodeunidade' => 'required'
        ]);
        if (!$validator->passes()) {
            \Session::flash('mensagem', ['msg' => 'Parâmetros insuficiente para o agendamento do Job.'
                , 'class' => 'red white-text']);
            return redirect()->back();
        } else {
            $superintendencias = $request->all(['superintendencia']);
            $tipodeunidade = $request->all(['tipodeunidade']);
            $ciclo = $request->all(['ciclo']);

//#########################################################################################################
//            para ativar a fila no console
//            php artisan queue:work --queue= geraXmlInspecao


            $job = (new JobXml_Inspecao ($superintendencias, $tipodeunidade, $ciclo))
                ->onQueue('geraXmlInspecao')->delay($dtnow->addMinutes(1));
            dispatch($job);

            \Session::flash('mensagem', ['msg' => 'Job Xml Inspecao aguardando processamento.'
                , 'class' => 'blue white-text']);
            return redirect()->back();

//   O valor de 134217728 bytes é equivalente a 128M

//   Erro de Memória.Isso ocorre porque no arquivo php.ini o parâmetro memory_limit está configurado para 128M. E para consertar o problema é só editar o arquivo e alterar para 256M.
//#########################################################################################################

            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            foreach ($superintendencias as $res) { // request é Array de indice para Superintendências
                foreach ($res as $superintendencia) { // percorre o Array de objeto Superintendências

                    // testa se o primeiro parâmetro é para todas superintendecia onde SE == 1
                    // Inicio do teste para todas superintendencias
                    if ($superintendencia == 1) {
                        // se verdadeiro se SE == 1 seleciona todas superintendência cujo a SE > 1

                        $inspecoes = DB::table('itensdeinspecoes')
                            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                            ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
                            ->where([['situacao', '=', 'Em Inspeção']])
                            ->where([['se', '=', 1]])
                            ->where([['inspecoes.ciclo', '=', $ciclo]])
                            ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
//                            ->where([['sto', '=', 16301561 ]]) // ACREUNA
                            //->limit(100)
                            ->get();
                        ini_set('memory_limit', '512M');
                        ini_set('max_input_time', 350);
                        ini_set('max_execution_time', 350);
                        // Início Gerar  XML

                        foreach ($inspecoes as $resp) {
                            $inspecao = Inspecao::find($resp->inspecao_id);
                            $situacao = 'AN';
                            $dtEncerramento = date('Y-m-d', strtotime($dtnow));
//                            dd($dtEncerramento);
//                            $dtEncerramento = '00:00:00';
                            $vazio = ' ';
                            $totalPontosNaInspeção = null;
                            $diretorio = "/xml/compliance/inspecao/";

                            $businessUnit = DB::table('unidades')
                                ->Where([['id', '=',  $inspecao->unidade_id]])
                                ->select('unidades.*')
                                ->first();

                            $registros = DB::table('itensdeinspecoes')
                                ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                                ->select('inspecoes.*'
                                    , 'itensdeinspecoes.*'
                                    ,'gruposdeverificacao.numeroGrupoVerificacao'
                                    ,'gruposdeverificacao.nomegrupo'
                                    ,'testesdeverificacao.numeroDoTeste'
                                    ,'testesdeverificacao.teste'
                                )
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                                ->get();

                            $row=0;
                            $totalPontosNaInspecao =  $registros->sum('pontuado');
// Gera  XML
                            $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
                            $xml .= '<rootelement>';
                            foreach ($registros as $registro) {
                                switch ($registro->avaliacao) {
                                    case "Conforme":
                                        $avaliacao='C';
                                        $comentario = $registro->oportunidadeAprimoramento .$registro->norma;
                                        break;
                                    case "Não Conforme":
                                        $avaliacao='N';
                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->evidencia ."\n".$registro->norma."\n".$registro->consequencias;
                                        break;
                                    case "Não Verificado":
                                        $avaliacao='V';
                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                        break;
                                    case "Não Executa Tarefa":
                                        $avaliacao='E';
                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                        break;
                                }

//                        testa se o registro foi avaliado, se não foi avaliado  a avaliação é nula para o registro
                                if ( $registro->eventosSistema == null) $avaliacao = null;

                                if ( $registro->itemQuantificado == 'Sim') {
                                    $quantificado = 'Quantificado';
                                    $falta = number_format($registro->valorFalta, 2, ',', '.');
                                    $sobra =number_format($registro->valorSobra, 2, ',', '.');
                                    $risco =number_format($registro->valorRisco, 2, ',', '.');
                                }
                                else {
                                    $quantificado = 'Não Quantificado';
                                    $falta = 0;
                                    $sobra = 0;
                                    $risco = 0;
                                }

                                if ( $registro->reincidencia == 'Sim') {
                                    $codVerificacaoAnterior =  $registro->codVerificacaoAnterior;
                                    $numeroGrupoReincidente =  $registro->numeroGrupoReincidente;
                                    $numeroItemReincidente =  $registro->numeroItemReincidente;
                                }
                                else {
                                    $codVerificacaoAnterior = ' ';
                                    $numeroGrupoReincidente =  ' ';
                                    $numeroItemReincidente =   ' ';

                                }

                                if (  $inspecao->tipoVerificacao=='Remoto') {
                                    $modalidade=1;
                                }
                                else {
                                    $modalidade=0;
                                }

                                $xml .= "\n\t".'<Dados>';
                                $xml .= "\n\t\t".'<RIP_Unidade>'.$inspecao->unidade->sto.'</RIP_Unidade>';
                                $xml .= "\n\t\t".'<RIP_NumInspecao>'.$inspecao->codigo.'</RIP_NumInspecao>';
                                $xml .= "\n\t\t".'<RIP_NumGrupo>'.$registro->numeroGrupoVerificacao.'</RIP_NumGrupo>';
                                $xml .= "\n\t\t".'<RIP_NumItem>'.$registro->numeroDoTeste.'</RIP_NumItem>';
                                $xml .= "\n\t\t".'<RIP_CodDiretoria>'.$inspecao->unidade->se.'</RIP_CodDiretoria>';

                                $xml .= "\n\t\t".'<RIP_CodReop>'.$businessUnit->mcu_subordinacaoAdm.'</RIP_CodReop>';
                                //isset($inspecao->reop_cod) ? $inspecao->reop_cod : ' '
                                $xml .= "\n\t\t".'<RIP_Ano>'.$inspecao->ciclo.'</RIP_Ano>';
                                $xml .= "\n\t\t".'<RIP_Resposta>'.$avaliacao.'</RIP_Resposta>';
                                $xml .= "\n\t\t".'<RIP_Comentario>'."\r\n\t\t\t".$comentario."\r\n\t\t".'</RIP_Comentario>';
                                $xml .= "\n\t\t".'<RIP_Caractvlr>'.$quantificado.'</RIP_Caractvlr>';
                                $xml .= "\n\t\t".'<RIP_Falta>'.$falta.'</RIP_Falta>';
                                $xml .= "\n\t\t".'<RIP_Sobra>'.$sobra.'</RIP_Sobra>';
                                $xml .= "\n\t\t".'<RIP_EmRisco>'.$risco.'</RIP_EmRisco>';
                                $xml .= "\n\t\t".'<RIP_DtUltAtu>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</RIP_DtUltAtu>';
                                $xml .= "\n\t\t".'<RIP_UserName>'.auth()->user()->document.'</RIP_UserName>';
                                $xml .= "\n\t\t".'<RIP_Recomendacoes>'.$registro->orientacao.' </RIP_Recomendacoes>';
                                $xml .= "\n\t\t".'<RIP_ReincInspecao>'.$codVerificacaoAnterior.'</RIP_ReincInspecao>';
                                $xml .= "\n\t\t".'<RIP_ReincGrupo>'.$numeroGrupoReincidente.'</RIP_ReincGrupo>';
                                $xml .= "\n\t\t".'<RIP_ReincItem>'.$numeroItemReincidente.'</RIP_ReincItem>';

//                        ################  INICIO verificar quais os campos corretos#############
//                        Tabelade itens deinspeção do SNCI ( RIP_Pontuado)
//                        Tabela de inspeções do SNCI (INP_TotalPontuado)
                                $xml .= "\n\t\t".'<RIP_Pontuado>'.$registro->pontuado.'</RIP_Pontuado>';
                                $xml .= "\n\t\t".'<INP_TotalPontuado>'.$totalPontosNaInspecao.'</INP_TotalPontuado>';
//                        ################ FIM  verificar quais os campos corretos#############

                                $xml .= "\n\t\t".'<INP_HrsPreInspecao>'.$registro->NumHrsPreInsp.'</INP_HrsPreInspecao>';
                                $xml .= "\n\t\t".'<INP_DtInicDeslocamento>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicDeslocamento>';
                                $xml .= "\n\t\t".'<INP_DtFimDeslocamento>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimDeslocamento>';
                                $xml .= "\n\t\t".'<INP_HrsDeslocamento>'.$registro->NumHrsDesloc.'</INP_HrsDeslocamento>';
                                $xml .= "\n\t\t".'<INP_DtInicInspecao>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicInspecao>';
                                $xml .= "\n\t\t".'<INP_DtFimInspecao>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimInspecao>';
                                $xml .= "\n\t\t".'<INP_HrsInspecao>'.$registro->NumHrsInsp.'</INP_HrsInspecao>';
                                $xml .= "\n\t\t".'<INP_Situacao>'.$situacao.'</INP_Situacao>';

//                        ################  Checar se a formatação do campo  INP_DtEncerramento está correta #############
                                $xml .= "\n\t\t".'<INP_DtEncerramento>'.$dtEncerramento.'</INP_DtEncerramento>';
                                $xml .= "\n\t\t".'<INP_Coordenador>'.$inspecao->inspetorcoordenador.'</INP_Coordenador>';
                                $xml .= "\n\t\t".'<INP_Responsavel>'.$businessUnit->documentRespUnidade.' - '.$businessUnit->nomeResponsavelUnidade.'</INP_Responsavel>';
                                $xml .= "\n\t\t".'<INP_Motivo>'.$vazio.'</INP_Motivo>';
                                $xml .= "\n\t\t".'<INP_Introducao>'.$vazio.'</INP_Introducao>';
                                $xml .= "\n\t\t".'<INP_Conclusao>'.$vazio.'</INP_Conclusao>';
                                $xml .= "\n\t\t".'<INP_Modalidade>'.$modalidade.'</INP_Modalidade>';
                                $xml .= "\n\t\t".'<IPT_MatricInspetor>'.$inspecao->inspetorcoordenador.$inspecao->inspetorcolaborador.'</IPT_MatricInspetor>';
                                $xml .= "\n\t\t".'<IPT_NumHrsPreInsp>'.$inspecao->NumHrsPreInsp.' '.$inspecao->NumHrsPreInsp.'</IPT_NumHrsPreInsp>';
                                $xml .= "\n\t\t".'<IPT_NumHrsDesloc>'.$inspecao->NumHrsDesloc.' '.$inspecao->NumHrsDesloc.'</IPT_NumHrsDesloc>';
                                $xml .= "\n\t\t".'<IPT_NumHrsInsp>'.$inspecao->NumHrsInsp.' '.$inspecao->NumHrsInsp.'</IPT_NumHrsInsp>';
                                $xml .= "\n\t".'</Dados>';
                            }
                            $xml .= "\n".'</rootelement>';
                            $arquivo = $inspecao->codigo.'_'.$inspecao->inspetorcoordenador.'.xml';
                            $inspecao->status = 'Em Análise';
                            $inspecao->eventoInspecao = $inspecao->eventoInspecao . "\r\n".'Em Análise Xml gerado por SgiWeb' ." em ".\Carbon\Carbon::parse(Carbon::now())->format( 'd/m/Y' );
                            $inspecao->xml = $diretorio.$arquivo;
//                          Atualiza dados da Inspeção
                            $inspecao->save();
//                          Move  o arquivo XML para a pasta de destino
                            $arquivo = preg_replace('/\\s\\s+/', ' ', $arquivo);
                            $fp = fopen($diretorio.$arquivo, 'w+');
                            fwrite($fp, $xml);
                            fclose($fp);
                            $row ++;
                            if ($row ==5) dd('parou - '.$row.' registros');
//                          FINALIZA o arquivo XML
                        }

                        //   final da geração do arqquivo XML
                        ini_set('memory_limit', '128M');
                        ini_set('max_input_time', 120);
                        ini_set('max_execution_time', 120);


                    }  // Fim do teste para todas superintendencias se superintendencia = 1

                    // inicio do testee para uma superintendencias
                    else {
                        $totalPontosNaInspecao = 0;

                        $inspecoes = DB::table('itensdeinspecoes')
                            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                            ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')

                            ->where([['status', '=', 'Em Inspeção']])  // após os testes comentar
//                          ->where([['status', '=', 'Inspecionado']]) // após os testes descomentar

                            ->where([['se', '=', $superintendencia]])
                            ->where([['inspecoes.ciclo', '=', $ciclo]])
                            ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
//                            ->where([['sto', '=', 16300882 ]]) // Anicuns
//                            ->where([['sto', '=', 16303407 ]]) // alto horizonte
//                            ->where([['sto', '=', 16300947 ]])   // Britania  16300947
//                            ->where([['mcu', '=', 6684 ]]) //ac anicuns
                            //->where([['itensdeinspecoes.testeVerificacao_id', '=', 3678 ]])  //3678  é smb_bdf
//                            ->limit(100)
                            ->get();

                        ini_set('memory_limit', '512M');
                        ini_set('max_input_time', 350);
                        ini_set('max_execution_time', 350);
                        // Início Gerar  XML

                        foreach ($inspecoes as $resp) {
                            $inspecao = Inspecao::find($resp->inspecao_id);
                            $situacao = 'AN';
                            $dtEncerramento = date('Y-m-d', strtotime($dtnow));
//                            dd($dtEncerramento);
//                            $dtEncerramento = '00:00:00';
                            $vazio = ' ';
                            $totalPontosNaInspeção = null;
                            $diretorio = "/xml/compliance/inspecao/";

                            $businessUnit = DB::table('unidades')
                                ->Where([['id', '=',  $inspecao->unidade_id]])
                                ->select('unidades.*')
                                ->first();

                            $registros = DB::table('itensdeinspecoes')
                                ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                                ->select('inspecoes.*'
                                    , 'itensdeinspecoes.*'
                                    ,'gruposdeverificacao.numeroGrupoVerificacao'
                                    ,'gruposdeverificacao.nomegrupo'
                                    ,'testesdeverificacao.numeroDoTeste'
                                    ,'testesdeverificacao.teste'
                                )
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                                ->get();

                            $row=0;
                            $totalPontosNaInspecao =  $registros->sum('pontuado');
// Gera  XML
                            $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
                            $xml .= '<rootelement>';
                            foreach ($registros as $registro) {
                                switch ($registro->avaliacao) {
                                    case "Conforme":
                                        $avaliacao='C';
                                        $comentario = $registro->oportunidadeAprimoramento .$registro->norma;
                                        break;
                                    case "Não Conforme":
                                        $avaliacao='N';
                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->evidencia ."\n".$registro->norma."\n".$registro->consequencias;
                                        break;
                                    case "Não Verificado":
                                        $avaliacao='V';
                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                        break;
                                    case "Não Executa Tarefa":
                                        $avaliacao='E';
                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                        break;
                                }



//                        testa se o registro foi avaliado, se não foi avaliado  a avaliação é nula para o registro
                                if ( $registro->eventosSistema == null) $avaliacao = null;

                                if ( $registro->itemQuantificado == 'Sim') {
                                    $quantificado = 'Quantificado';
                                    $falta = number_format($registro->valorFalta, 2, ',', '.');
                                    $sobra =number_format($registro->valorSobra, 2, ',', '.');
                                    $risco =number_format($registro->valorRisco, 2, ',', '.');
                                }
                                else {
                                    $quantificado = 'Não Quantificado';
                                    $falta = 0;
                                    $sobra = 0;
                                    $risco = 0;
                                }

                                if ( $registro->reincidencia == 'Sim') {
                                    $codVerificacaoAnterior =  $registro->codVerificacaoAnterior;
                                    $numeroGrupoReincidente =  $registro->numeroGrupoReincidente;
                                    $numeroItemReincidente =  $registro->numeroItemReincidente;
                                }
                                else {
                                    $codVerificacaoAnterior = ' ';
                                    $numeroGrupoReincidente =  ' ';
                                    $numeroItemReincidente =   ' ';

                                }
                                //   identificar qual a modalidade será para inspeção monitorada. e ajustar o if a seguir
                                if (  $inspecao->tipoVerificacao == 'Remoto') {
                                    $modalidade=1;
                                }
                                else {
                                    $modalidade=0;
                                }

//                                fazer uma rotina para gravar OS ITENS NÃO CONFORME da inspeção na tabela SNCI
//################################   colocar a rotina aqui   gravar o item não conforme e os campos na tabela SNCI
//MANTER HISTÓRICO DAS INSPEÇÕES

                                $xml .= "\n\t".'<Dados>';
                                $xml .= "\n\t\t".'<RIP_Unidade>'.$inspecao->unidade->sto.'</RIP_Unidade>';
                                $xml .= "\n\t\t".'<RIP_NumInspecao>'.$inspecao->codigo.'</RIP_NumInspecao>';
                                $xml .= "\n\t\t".'<RIP_NumGrupo>'.$registro->numeroGrupoVerificacao.'</RIP_NumGrupo>';
                                $xml .= "\n\t\t".'<RIP_NumItem>'.$registro->numeroDoTeste.'</RIP_NumItem>';
                                $xml .= "\n\t\t".'<RIP_CodDiretoria>'.$inspecao->unidade->se.'</RIP_CodDiretoria>';

                                $xml .= "\n\t\t".'<RIP_CodReop>'.$businessUnit->mcu_subordinacaoAdm.'</RIP_CodReop>';
                                //isset($inspecao->reop_cod) ? $inspecao->reop_cod : ' '
                                $xml .= "\n\t\t".'<RIP_Ano>'.$inspecao->ciclo.'</RIP_Ano>';
                                $xml .= "\n\t\t".'<RIP_Resposta>'.$avaliacao.'</RIP_Resposta>';
                                $xml .= "\n\t\t".'<RIP_Comentario>'."\r\n\t\t\t".$comentario."\r\n\t\t".'</RIP_Comentario>';
                                $xml .= "\n\t\t".'<RIP_Caractvlr>'.$quantificado.'</RIP_Caractvlr>';
                                $xml .= "\n\t\t".'<RIP_Falta>'.$falta.'</RIP_Falta>';
                                $xml .= "\n\t\t".'<RIP_Sobra>'.$sobra.'</RIP_Sobra>';
                                $xml .= "\n\t\t".'<RIP_EmRisco>'.$risco.'</RIP_EmRisco>';
                                $xml .= "\n\t\t".'<RIP_DtUltAtu>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</RIP_DtUltAtu>';
                                $xml .= "\n\t\t".'<RIP_UserName>'.auth()->user()->document.'</RIP_UserName>';
                                $xml .= "\n\t\t".'<RIP_Recomendacoes>'.$registro->orientacao.' </RIP_Recomendacoes>';
                                $xml .= "\n\t\t".'<RIP_ReincInspecao>'.$codVerificacaoAnterior.'</RIP_ReincInspecao>';
                                $xml .= "\n\t\t".'<RIP_ReincGrupo>'.$numeroGrupoReincidente.'</RIP_ReincGrupo>';
                                $xml .= "\n\t\t".'<RIP_ReincItem>'.$numeroItemReincidente.'</RIP_ReincItem>';

//                        ################  INICIO verificar quais os campos corretos#############
//                        Tabelade itens deinspeção do SNCI ( RIP_Pontuado)
//                        Tabela de inspeções do SNCI (INP_TotalPontuado)
                                $xml .= "\n\t\t".'<RIP_Pontuado>'.$registro->pontuado.'</RIP_Pontuado>';

                                $xml .= "\n\t\t".'<INP_TotalPontuado>'.$totalPontosNaInspecao.'</INP_TotalPontuado>';
//                        ################ FIM  verificar quais os campos corretos#############

                                $xml .= "\n\t\t".'<INP_HrsPreInspecao>'.$registro->NumHrsPreInsp.'</INP_HrsPreInspecao>';
                                $xml .= "\n\t\t".'<INP_DtInicDeslocamento>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicDeslocamento>';
                                $xml .= "\n\t\t".'<INP_DtFimDeslocamento>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimDeslocamento>';
                                $xml .= "\n\t\t".'<INP_HrsDeslocamento>'.$registro->NumHrsDesloc.'</INP_HrsDeslocamento>';
                                $xml .= "\n\t\t".'<INP_DtInicInspecao>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicInspecao>';
                                $xml .= "\n\t\t".'<INP_DtFimInspecao>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimInspecao>';
                                $xml .= "\n\t\t".'<INP_HrsInspecao>'.$registro->NumHrsInsp.'</INP_HrsInspecao>';
                                $xml .= "\n\t\t".'<INP_Situacao>'.$situacao.'</INP_Situacao>';

//                        ################  Checar se a formatação do campo  INP_DtEncerramento está correta #############
                                $xml .= "\n\t\t".'<INP_DtEncerramento>'.$dtEncerramento.'</INP_DtEncerramento>';
                                $xml .= "\n\t\t".'<INP_Coordenador>'.$inspecao->inspetorcoordenador.'</INP_Coordenador>';
                                $xml .= "\n\t\t".'<INP_Responsavel>'.$businessUnit->documentRespUnidade.' - '.$businessUnit->nomeResponsavelUnidade.'</INP_Responsavel>';
                                $xml .= "\n\t\t".'<INP_Motivo>'.$vazio.'</INP_Motivo>';
                                $xml .= "\n\t\t".'<INP_Introducao>'.$vazio.'</INP_Introducao>';
                                $xml .= "\n\t\t".'<INP_Conclusao>'.$vazio.'</INP_Conclusao>';
                                $xml .= "\n\t\t".'<INP_Modalidade>'.$modalidade.'</INP_Modalidade>';
                                $xml .= "\n\t\t".'<IPT_MatricInspetor>'.$inspecao->inspetorcoordenador.$inspecao->inspetorcolaborador.'</IPT_MatricInspetor>';
                                $xml .= "\n\t\t".'<IPT_NumHrsPreInsp>'.$inspecao->NumHrsPreInsp.' '.$inspecao->NumHrsPreInsp.'</IPT_NumHrsPreInsp>';
                                $xml .= "\n\t\t".'<IPT_NumHrsDesloc>'.$inspecao->NumHrsDesloc.' '.$inspecao->NumHrsDesloc.'</IPT_NumHrsDesloc>';
                                $xml .= "\n\t\t".'<IPT_NumHrsInsp>'.$inspecao->NumHrsInsp.' '.$inspecao->NumHrsInsp.'</IPT_NumHrsInsp>';
                                $xml .= "\n\t".'</Dados>';
                            }
                            $xml .= "\n".'</rootelement>';
                            $arquivo = $inspecao->codigo.'_'.$inspecao->inspetorcoordenador.'.xml';
                            $inspecao->status = 'Em Análise';
                            $inspecao->eventoInspecao = $inspecao->eventoInspecao . "\r\n".'Em Análise Xml gerado por SgiWeb' ." em ".\Carbon\Carbon::parse(Carbon::now())->format( 'd/m/Y' );
                            $inspecao->xml = $diretorio.$arquivo;
//                          Atualiza dados da Inspeção
                            $inspecao->save();
//                          Move  o arquivo XML para a pasta de destino
                            $arquivo = preg_replace('/\\s\\s+/', ' ', $arquivo);
                            $fp = fopen($diretorio.$arquivo, 'w+');
                            fwrite($fp, $xml);
                            fclose($fp);

//                          FINALIZA o arquivo XML
                        }

                        //   final da geração do arqquivo XML
                        ini_set('memory_limit', '128M');
                        ini_set('max_input_time', 120);
                        ini_set('max_execution_time', 120);
                    } // Fim  para gerar xml por superintendencias
                }
            }
        }
    }

    public function xml() {
        $status = 'Criado e instalado';
        try {
            $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        }catch ( \Exception $e){
            return redirect()->route('login');
        }

        if (!empty($businessUnitUser)) {
            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id) {
                case 1:
                case 2:
                    {
                        $registros = DB::table('unidades')
                            ->select(
                                'id', 'se', 'seDescricao'
                            )
                            ->where([['se', '>', 1]])
                            ->where([['status_unidadeDesc', '=', $status]])
                            ->groupBy('se')
                            ->orderBy('seDescricao', 'asc')
                            ->get();

                    }
                    break;
            }

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
                ->get();

            return view('compliance.monitoramento.gerar_xml', compact('registros', 'tiposDeUnidade'));
        } else {
            \Session::flash('mensagem', ['msg' => 'Não foi possivel exibir os itens você provavelmente não é administrador.'
                , 'class' => 'red white-text']);
            return redirect()->route('home');
        }
    }

    public function transformDate($value, $format = 'Y-m-d') {
        try {
            return Carbon::instance(
//                Date ::excelToDateTimeObject($value));
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\ErrorException $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }

}
