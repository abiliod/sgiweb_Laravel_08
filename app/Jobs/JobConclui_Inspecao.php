<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobConclui_Inspecao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $superintendencias, $tipodeunidade, $ciclo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($superintendencias, $tipodeunidade, $ciclo)
    {
        $this->superintendencias = $superintendencias;
        $this->tipodeunidade = $tipodeunidade;
        $this->ciclo = $ciclo;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dtnow = new Carbon();
        $superintendencias = $this->superintendencias;
        $tipodeunidade = $this->tipodeunidade;
        $ciclo = $this->ciclo;
        ini_set('memory_limit', '512M');
        foreach ($superintendencias as $res) { // request é Array de indice para Superintendências
            foreach ($res as $superintendencia) { // percorre o Array de objeto Superintendências
                // testa se o primeiro parâmetro é para todas superintendecia onde SE == 1
                // Inicio do teste para todas superintendencias
                if ($superintendencia == 1) {
                    // se verdadeiro se SE == 1 seleciona todas superintendência cujo a SE > 1
//                      Início Todas Inspeções da Superintendencia
                    $inspecoes = DB::table( 'unidades' )
                        ->join('inspecoes', 'unidades.id', '=', 'inspecoes.unidade_id')
                        ->select('inspecoes.*'   )
                        ->where([['unidades.se', '=', $superintendencia ]])
                        ->where([['inspecoes.ciclo', '=', $ciclo ]])
                        ->where([['inspecoes.status', '=', 'Em Inspeção' ]])
                        ->where([['inspecoes.tipoVerificacao', '=', 'Monitorada' ]])
                        ->where([['unidades.tipoUnidade_id', '=', $tipodeunidade ]])
                        ->get();
//                      Início Todas Inspeções da Superintendencia
                    foreach ($inspecoes as $inspecao){
//                          inicio percorrida das inspeções
                        $totalPontos = 0;
                        $valor_ref_itens_inspecionados = 0;
                        $totalpontosnaoconforme =0;
                        $totalitensnaoconforme = 0;
                        $classificacao = null;
                        $pontuacaoFinal=0;

                        $itensdeinspecao = DB::table('itensdeinspecoes')
                            ->where([['inspecao_id', '=', $inspecao->id]])
                            ->where([['situacao', '=', 'Inspecionado']])
                            ->orderBy('testeVerificacao_id' , 'asc')
                            ->get();

//                            dd($itensdeinspecao);

                        if (!empty($itensdeinspecao)) $totalPontos =  $itensdeinspecao->sum('pontuado');

                        $totalitensavaliados =  $itensdeinspecao->count('eventosSistema');

//                      Inicio itens das inspeções  == Registro
                        foreach ($itensdeinspecao as $registro) {
                            $consequencias = $registro->consequencias;
                            $eventosSistema = "Corroborado remotamente por Websgi em ".date('d/m/Y H:m:s', strtotime($dtnow))
                                ."\n"
                                .$registro->eventosSistema;
                            $situacao = null;

                            if ($registro->avaliacao == 'Não Conforme') {
                                $testesdeverificacao = DB::table('testesdeverificacao')
                                    ->select('testesdeverificacao.*')
                                    ->Where([['id', '=', $registro->testeVerificacao_id]])
                                    ->first();
                                $totalitensnaoconforme++;
                                $totalpontosnaoconforme = $totalpontosnaoconforme + $registro->pontuado;
                                $valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados + $testesdeverificacao->maximodepontos;
                                $situacao = 'Não Respondido';

                            }
                            else{
                                $consequencias = null;
                                $situacao = 'Concluido' ;
                            }

//                           GRAVA ITEM DA INSPEÇÃO
                            DB::table('itensdeinspecoes')
                                ->where([['id', '=', $registro->id]])
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->update([
                                    'situacao' => $situacao,
                                    'eventosSistema' => $eventosSistema,
                                    'consequencias' => $consequencias
                                ]);
//                           GRAVA ITEM DA INSPEÇÃO

                        }
//                      Fim itens da inspeção  == Registro

#################   CALCULA TNC
                        if ($valor_ref_itens_inspecionados == 0) {
                            $tnc = 0;
                        }
                        else{
                            $tnc = ( $totalpontosnaoconforme / $valor_ref_itens_inspecionados)*100;
                        }
#################   CALCULA TNC

#################   CLASSIFICA A  INSPEÇÃO
                        if($tnc > 50){
                            $classificacao = 'Controle ineficaz';
                            $status = 'Em Inspeção';
                        }
                        elseif ($tnc > 20 ){
                            $classificacao = 'Controle pouco eficaz';
                            $status = 'Em Inspeção';

                        }
                        elseif ($tnc > 10 ){
                            $classificacao = 'Controle de eficácia mediana';
                            $status = 'Corroborado';
                            $pontuacaoFinal= $totalPontos;
                        }
                        elseif ($tnc > 5 ){
                            $classificacao = 'Controle eficaz';
                            $status = 'Corroborado';
                            $pontuacaoFinal = $totalPontos;
                        }
                        elseif(($tnc > 0 ) && ($tnc <= 5 )){
                            $classificacao = 'Controle plenamente eficaz';
                            $status = 'Corroborado';
                            $pontuacaoFinal = $totalPontos;
                        }
                        elseif ($tnc === 0 ){
                            $classificacao = 'Controle plenamente eficaz';
                            $status = 'Concluida' ;
                            $pontuacaoFinal = $totalPontos;
                        }
#################   CLASSIFICA A  INSPEÇÃO

############  GRAVA A INSPEÇÃO
                        DB::table('inspecoes')
                            ->where([['id', '=', $inspecao->id]])
                            ->update([
                                'totalPontos' => $totalPontos,
                                'totalitensavaliados' => $totalitensavaliados,
                                'totalitensnaoconforme' => $totalitensnaoconforme,
                                'totalpontosnaoconforme' => $totalpontosnaoconforme,
                                'valor_ref_itens_inspecionados' => $valor_ref_itens_inspecionados,
                                'tnc' => $tnc,
                                'status' => $status,
                                'pontuacaoFinal' =>  $pontuacaoFinal,
                                'classificacao' => $classificacao
                            ]);

############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
                        if  ($tnc > 20){
                            DB::table('itensdeinspecoes')
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->where([['avaliacao', '=', 'Não Conforme']])
                                ->update([
                                    'situacao' =>  'Em Inspeção'
                                ]);
                        }
############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL

############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
                        if  (($tnc < 20) && ($totalitensnaoconforme>1)){
                            DB::table('itensdeinspecoes')
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->where([['avaliacao', '=', 'Não Conforme']])
                                ->update([
                                    'situacao' =>  'Não Respondido'
                                ]);
                        }
############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
                    }
//                      Fim percorrida das inspeções
                }

                // Fim do teste para todas superintendencias se superintendencia = 1

                // inicio do testee para uma superintendencias
                else {

//                      Início Todas Inspeções da Superintendencia
                    $inspecoes = DB::table( 'unidades' )
                        ->join('inspecoes', 'unidades.id', '=', 'inspecoes.unidade_id')
                        ->select('inspecoes.*'   )
                        ->where([['unidades.se', '=', $superintendencia ]])
                        ->where([['inspecoes.ciclo', '=', $ciclo ]])
                        ->where([['inspecoes.status', '=', 'Em Inspeção' ]])
                        ->where([['inspecoes.tipoVerificacao', '=', 'Monitorada' ]])
                        ->where([['unidades.tipoUnidade_id', '=', $tipodeunidade ]])
                        ->get();
//                      Início Todas Inspeções da Superintendencia
                    foreach ($inspecoes as $inspecao){
//                          inicio percorrida das inspeções
                        $totalPontos = 0;
                        $totalitensavaliados = 0;
                        $valor_ref_itens_inspecionados = 0;
                        $totalpontosnaoconforme =0;
                        $totalitensnaoconforme = 0;
                        $classificacao = null;
                        $pontuacaoFinal=0;

                        $itensdeinspecao = DB::table('itensdeinspecoes')
                            ->where([['inspecao_id', '=', $inspecao->id]])
                            ->where([['situacao', '=', 'Inspecionado']])
                            ->orderBy('testeVerificacao_id' , 'asc')
                            ->get();

//                            dd($itensdeinspecao);

                        if (!empty($itensdeinspecao)) $totalPontos =  $itensdeinspecao->sum('pontuado');

//                      Inicio itens das inspeções  == Registro
                        foreach ($itensdeinspecao as $registro){
                            $consequencias = $registro->consequencias;
                            $eventosSistema = "Corroborado remotamente por Websgi em ".date('d/m/Y H:m:s', strtotime($dtnow))
                                ."\n"
                                .$registro->eventosSistema;
                            $situacao = null;

                            if ($registro->avaliacao == 'Não Conforme') {
                                $testesdeverificacao = DB::table('testesdeverificacao')
                                    ->select('testesdeverificacao.*')
                                    ->Where([['id', '=', $registro->testeVerificacao_id]])
                                    ->first();
                                $totalitensnaoconforme++;
                                $totalpontosnaoconforme = $totalpontosnaoconforme + $registro->pontuado;
                                $valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados + $testesdeverificacao->maximodepontos;
                                $situacao = 'Não Respondido';

                            }
                            else{
                                $consequencias = null;
                                $situacao = 'Concluido' ;
                            }

//                           GRAVA ITEM DA INSPEÇÃO
                            DB::table('itensdeinspecoes')
                                ->where([['id', '=', $registro->id]])
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->update([
                                    'situacao' => $situacao,
                                    'eventosSistema' => $eventosSistema,
                                    'consequencias' => $consequencias
                                ]);
//                           GRAVA ITEM DA INSPEÇÃO

                        }
//                      Fim itens da inspeção  == Registro

#################   CALCULA TNC
                        if ($valor_ref_itens_inspecionados == 0) {
                            $tnc = 0;
                        }
                        else{
                            $tnc = ( $totalpontosnaoconforme / $valor_ref_itens_inspecionados)*100;
                        }
#################   CALCULA TNC

#################   CLASSIFICA A  INSPEÇÃO
                        if($tnc > 50){
                            $classificacao = 'Controle ineficaz';
                            $status = 'Em Inspeção';
                        }
                        elseif ($tnc > 20 ){
                            $classificacao = 'Controle pouco eficaz';
                            $status = 'Em Inspeção';

                        }
                        elseif ($tnc > 10 ){
                            $classificacao = 'Controle de eficácia mediana';
                            $status = 'Corroborado';
                            $pontuacaoFinal= $totalPontos;
                        }
                        elseif ($tnc > 5 ){
                            $classificacao = 'Controle eficaz';
                            $status = 'Corroborado';
                            $pontuacaoFinal = $totalPontos;
                        }
                        elseif(($tnc > 0 ) && ($tnc <= 5 )){
                            $classificacao = 'Controle plenamente eficaz';
                            $status = 'Corroborado';
                            $pontuacaoFinal = $totalPontos;
                        }
                        elseif ($tnc === 0 ){
                            $classificacao = 'Controle plenamente eficaz';
                            $status = 'Concluida' ;
                            $pontuacaoFinal = $totalPontos;
                        }

#################   CLASSIFICA A  INSPEÇÃO

############  GRAVA A INSPEÇÃO
                        DB::table('inspecoes')
                            ->where([['id', '=', $inspecao->id]])
                            ->update([
                                'totalPontos' => $totalPontos,
                                'totalitensavaliados' => $totalitensavaliados,
                                'totalitensnaoconforme' => $totalitensnaoconforme,
                                'totalpontosnaoconforme' => $totalpontosnaoconforme,
                                'valor_ref_itens_inspecionados' => $valor_ref_itens_inspecionados,
                                'tnc' => $tnc,
                                'status' => $status,
                                'pontuacaoFinal' =>  $pontuacaoFinal,
                                'inspetorcoordenador' =>  null,
                                'inspetorcolaborador' =>  null,
                                'classificacao' => $classificacao
                            ]);

############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
                        if  ($tnc > 20){
                            DB::table('itensdeinspecoes')
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->where([['avaliacao', '=', 'Não Conforme']])
                                ->update([
                                    'situacao' =>  'Em Inspeção'
                                ]);
                        }
############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL

############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
                        if  (($tnc < 20) && ($totalitensnaoconforme>1)){
                            DB::table('itensdeinspecoes')
                                ->where([['inspecao_id', '=', $inspecao->id]])
                                ->where([['avaliacao', '=', 'Não Conforme']])
                                ->update([
                                    'situacao' =>  'Não Respondido'
                                ]);
                        }
############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
                    }
//                      Fim percorrida das inspeções
                }
//                      Fim Todas Inspeções da Superintendencia

            }

        }
        ini_set('memory_limit', '128M');
    }
}
