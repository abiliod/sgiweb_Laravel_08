<?php

namespace App\Jobs;


use App\Models\Correios\Inspecao;
use App\Models\Correios\Itensdeinspecao;
use App\Models\Correios\SequenceInspecao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GeraInspecao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected   $superintendencias, $status , $ciclo, $dataAgenda;
    public function __construct(  $superintendencias, $status , $ciclo, $dataAgenda )
    {
        $this->superintendencias = $superintendencias;
        $this->status = $status;
        $this->ciclo = $ciclo;
        $this->dataAgenda = $dataAgenda;
        //dd($superintendencias, $status , $ciclo, $dataAgenda);
    }

    public function handle()
    {
        $superintendencias = $this->superintendencias;
        $status  = $this->status;
        $ciclo  = $this->ciclo;
        $dataAgenda = $this->dataAgenda;

        // php artisan queue:work --queue=geraInspecao
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
