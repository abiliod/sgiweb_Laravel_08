<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\Cadastral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobCadastral implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected   $cadastrals, $dt;

    public function __construct(  $cadastrals , $dt )
    {
        $this->cadastrals = $cadastrals;
        $this->dt = $dt;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cadastrals = $this->cadastrals;
        $dt  = $this->dt;
        ini_set('memory_limit', '512M');
        foreach($cadastrals as $registros)
        {
            foreach($registros as $dado)
            {
                Cadastral :: updateOrCreate([
                    'matricula' => $dado['matricula']
                ],[
                    'matricula' => $dado['matricula']
                    ,'se' => $dado['secs']
                    ,'mcu' => (int)$dado['mcu']
                    ,'lotacao' => $dado['lotacao']
                    ,'nome_do_empregado' => $dado['nome']
                    ,'cargo' => $dado['cargo']
                    ,'especializ' => $dado['especialidade']
                    ,'funcao' => $dado['funcao']
                    ,'sexo' => $dado['ppes_sexo']
                    ,'situacao' => 'ATIVO'
                ]);
            }
            DB::table('cadastral')
                ->where('se', $dado['secs'])
                ->where('updated_at', '<', $dt)
                ->update(['situacao' => null]);
        }
        ini_set('memory_limit', '64M');
    }
}
