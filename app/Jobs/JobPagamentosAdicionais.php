<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\PagamentosAdicionais;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobPagamentosAdicionais implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected  $pagamentos_adicionais, $dt_job, $ref;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pagamentos_adicionais, $dt_job, $ref)
    {
        $this->pagamentos_adicionais = $pagamentos_adicionais;
        $this->dt_job = $dt_job;
        $this->ref = $ref;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pagamentos_adicionais = $this->pagamentos_adicionais;
        $ref = $this->ref;

        foreach($pagamentos_adicionais as $dados)
        {
            foreach($dados as $registro)
            {
                $matricula = $registro['matricula'];
                while( $matricula !== null )
                {
                    PagamentosAdicionais::updateOrCreate([
                        'se' => $registro['se']
                        ,'sigla_lotacao' => $registro['sigla_lotacao']
                        ,'matricula' => $registro['matricula']
                        ,'rubrica' => $registro['rubrica']
                        ,'ref' => $registro['ref']
                    ],[
                        'se' => $registro['se']
                        ,'sigla_lotacao' => $registro['sigla_lotacao']
                        ,'matricula' => $registro['matricula']
                        ,'rubrica' => $registro['rubrica']
                        ,'ref' => $registro['ref']
                        ,'nome' => $registro['nome']
                        ,'cargo' => $registro['cargo']
                        ,'espec' => $registro['espec']
                        ,'titular_da_funcao' => $registro['titular_da_funcao']
                        ,'dif_mer' => $registro['dif_mer']
                        ,'qtd' => $registro['qtd']
                        ,'valor' => $registro['valor']
                    ]);
                    $matricula = null;
                }
            }
        }
        DB::table('pagamentos_adicionais')->where('ref', '<', $ref)->delete();

    }
}
