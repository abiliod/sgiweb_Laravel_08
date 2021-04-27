<?php

namespace App\Http\Controllers\Correios\Importacao;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use App\Models\Correios\ModelsAuxiliares\FeriasPorMcu;
use App\Imports\ImportFeriasPorMcu;
use App\Jobs\JobFeriasPorMCU;

use App\Models\Correios\ModelsAuxiliares\Proter;
use App\Imports\ImportProter;
use App\Exports\ExportProter;
use App\Jobs\JobProter;

use App\Models\Correios\ModelsAuxiliares\DebitoEmpregado;
use App\Imports\ImportDebitoEmpregados;
use App\Exports\ExportDebitoEmpregados;
use App\Jobs\JobWebCont;

use App\Models\Correios\Alarme;
use App\Imports\ImportAlarmes;
use App\Exports\ExportAlarmes;
use App\Jobs\JobAlarmes;

use App\Models\Correios\ModelsAuxiliares\Absenteismo;
use App\Imports\ImportAbsenteismo;
use App\Exports\ExportAbsenteismo;
use App\Jobs\JobAbsenteismo;


use App\Models\Correios\ModelsAuxiliares\ControleDeViagem;
use App\Imports\ImportControleDeViagem;
use App\Exports\ExportControleDeViagem;
use App\Jobs\JobControleViagem;

use App\Models\Correios\ModelsAuxiliares\PLPListaPendente;
use App\Imports\ImportPLPListaPendente;
use App\Exports\ExportPLPListaPendente;
use App\Jobs\JobPLPpendente;

use App\Models\Correios\ModelsAuxiliares\SgdoDistribuicao;
use App\Imports\ImportSgdoDistribuicao;
use App\Exports\ExportSgdoDistribuicao;
use App\Jobs\JobSgdoDistribuicao;

use App\Models\Correios\ModelsAuxiliares\CieEletronica;
use App\Imports\ImportCieEletronica;
use App\Exports\ExportCieEletronica;
use App\Jobs\JobCieEletronica;

use App\Models\Correios\ModelsAuxiliares\PainelExtravio;
use App\Imports\ImportPainelExtravio;
use App\Exports\ExportPainelExtravio;
use App\Jobs\JobPainelExtravio;

use App\Models\Correios\ModelsAuxiliares\PagamentosAdicionais;
use App\Imports\ImportPagamentosAdicionais;
use App\Exports\ExportPagamentosAdicionais;
use App\Jobs\JobPagamentosAdicionais;

use App\Models\Correios\ModelsAuxiliares\BDF_FAT_02;
use App\Imports\ImportBDF_FAT_02;
use App\Exports\ExportBDF_FAT_02;
use App\Jobs\Job_BDF_FAT_02;

use App\Models\Correios\ModelsAuxiliares\MicroStrategy;
use App\Imports\ImportMicroStrategy;
use App\Exports\ExportMicroStrategy;
use App\Jobs\JobMicroStrategy;

use App\Exports\ExportSnci;
use App\Imports\ImportSnci;
use App\Jobs\JobSnci;

use App\Imports\ImportCadastral;
use App\Exports\ExportCadastral;
use App\Jobs\JobCadastral;

use App\Imports\ImportControleDeViagemApontamentos;
//use App\Exports\ExportControleDeViagem;
use App\Models\Correios\ModelsAuxiliares\ApontamentoCV;
use App\Jobs\JobApontamentoCV;

use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use App\Imports\ImportSL02_bdf;
use App\Exports\ExportSL02_bdf;
use App\Jobs\JobSLD_02_BDF;

use App\Models\Correios\Unidade;
use App\Imports\ImportUnidades;
use App\Models\Correios\UnidadeEndereco;
use App\Jobs\JobUnidades;
use App\Jobs\JobHorariosDERAT;


use App\Models\Correios\ModelsAuxiliares\SMBxBDF_NaoConciliado;
use App\Imports\ImportSMBxBDF_NaoConciliado;
use App\Exports\ExportSMBxBDF_NaoConciliado;

//   ################## INICIO   PENDENTES DE CRIAR ROTINAS DE JOBs   ########################

use App\Models\Correios\ModelsAuxiliares\RespDefinida;
use App\Imports\ImportRespDefinida;
use App\Exports\ExportRespDefinida;

// Amilton, me liga quando for importar Feriados, te explico
// você vai aproveitar os dados exixtentes já cadastrados  Abilio....
use App\Models\Correios\ModelsAuxiliares\Feriado;
use App\Imports\ImportFeriado;
use App\Exports\ExportFeriado;


use App\Models\Correios\ModelsAuxiliares\Cftv;
use App\Imports\ImportCftv;
use App\Exports\ExportCftv;

//   ################## FINAL   PENDENTES DE CRIAR ROTINAS DE JOBs   ########################




class ImportacaoController extends Controller
{

    // ######################### INICIO   BDF_FAT_02 ##################
    public function exportBDF_FAT_02()
    {
        return Excel::download(new ExportBDF_FAT_02, 'bdf_fat_02.xlsx');
    }
    public function importBDF_FAT_02(Request $request) {

        $dtmenos210dias = Carbon::now();
        $dtmenos210dias->subDays(210);
        $dt_job = Carbon::now();
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;
        $extencion = $request->file('file')->getClientOriginalExtension();

        if (($size > 500) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
//dd($extencion);

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $bdf_fat_02 = Excel::toArray(new ImportBDF_FAT_02,  request()->file('file'));

            try {
                //  php artisan queue:work --queue=importacao
                $job = (new Job_BDF_FAT_02($bdf_fat_02,$dtmenos210dias, $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'Job_BDF_FAT_02, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'Job_BDF_FAT_02, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'Job_BDF_FAT_02, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode ser importado formato inválido '. $extencion .' deve ser (xlsx).'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

    }
    public function bdf_fat_02()
    {
        return view('compliance.importacoes.bdf_fat_02');  //
    }
    // ######################### FIM  BDF_FAT_02 #######################

    // ################### INICIO    Pagamentos adicionais  #############
    public function exportpagamentosAdicionais() {
        return Excel::download(new ExportPagamentosAdicionais, 'pagamentosAdicionais.xlsx');
    }
    public function importPagamentosAdicionais(Request $request) {
        $dtmenos10meses = new Carbon();
        $dtmenos10meses->subMonth(10);
        $ref = substr($dtmenos10meses,0,4). substr($dtmenos10meses,5,2);
        $dt_job = Carbon::now();
//variaveis

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;
        $extencion = $request->file('file')->getClientOriginalExtension();

        if (($size > 500) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
//dd($extencion);

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $pagamentos_adicionais = Excel::toArray(new ImportPagamentosAdicionais,  request()->file('file'));

            try {
                //  php artisan queue:work --queue=importacao
//                $job = (new JobPagamentosAdicionais($pagamentos_adicionais,$ref, $dt_job))
//                    ->onConnection('importacao')
//                    ->onQueue('importacao')
//                    ->delay($dt_job->addMinutes(1));
//                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobPagamentosAdicionais, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobPagamentosAdicionais, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobPagamentosAdicionais, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode ser importado formato inválido '. $extencion .' deve ser (xlsx).'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

    }

    public function pagamentosAdicionais()
    {
        return view('compliance.importacoes.pagamentosAdicionais');  //
    }
    // ######################### FIM  Pagamentos adicionais  #############

    // ################### INICIO    cie eletronica  ################
    public function exportcieEletronica()
    {
        return Excel::download(new ExportCieEletronica, 'cieEletronica.xlsx');
    }
    public function importCieEletronica(Request $request)  {

        $dt_job = Carbon::now();
        $emissao       = null;
        $data_de_resposta      = null;
        $dtmenos365dias = Carbon::now();
        $dtmenos365dias = $dtmenos365dias->subDays(365);

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;

        if (($size > 500) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);
            $cie_eletronicas = Excel::toArray(new ImportCieEletronica, request()->file('file'));
            try {
                //  php artisan queue:work --queue=importacao
                $job = (new JobCieEletronica($cie_eletronicas, $dt_job, $dtmenos365dias))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);
                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobCieEletronica, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobCieEletronica, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobCieEletronica, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }
        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode Ser importado Houve um erro.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
    }

    public function cieEletronica()
    {
        return view('compliance.importacoes.cieEletronica');  //
    }
    // ################### FIM  cieEletronica #######################

    // ######################### INICIO    Painel de extravio  ##############
    public function exportpainelExtravio()
    {
        return Excel::download(new ExportPainelExtravio, 'painelExtravio.xlsx');
    }
    public function importPainelExtravio(Request $request) {

        $dtmenos360dias = Carbon::now();
        $dtmenos360dias = $dtmenos360dias->subDays(360);
        $dt_job = Carbon::now();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo. Não foi Selecionado!'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;

        if (($size > 500) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $painel_extravios = Excel::toArray(new ImportPainelExtravio,  request()->file('file'));

            try {
                //  php artisan queue:work --queue=importacao
                $job = (new JobPainelExtravio($painel_extravios, $dt_job, $dtmenos360dias))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobPainelExtravio, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobPainelExtravio, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobPainelExtravio, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode Ser importado Houve um erro.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
    }
    public function painelExtravio()
    {
        return view('compliance.importacoes.painelExtravio');  //
    }
    // ######################### FIM  painel de extravio ####################

    // ######################### INICIO   Microestraetegy ##################
    public function exportMicroStrategy()
    {
        return Excel::download(new ExportMicroStrategy, 'microStrategy.xlsx');
    }

    public function importMicroStrategy(Request $request) {
        $row = 0;
        $dtmenos210dias = Carbon::now();
        $dtmenos210dias = $dtmenos210dias->subDays(210);
        $dt_job = Carbon::now();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser 277-2-4_3-ObjetosNaoEntreguePrimeiraTentativa.xlsx ! Selecione Corretamente'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;

        if (($size > 500) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $micro_strategy = Excel::toArray(new ImportMicroStrategy, request()->file('file'));
            try {
                //  php artisan queue:work --queue=importacao
                $job = (new JobMicroStrategy($micro_strategy, $dt_job, $dtmenos210dias))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobMicroStrategy, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobMicroStrategy, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobMicroStrategy, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode Ser importado Houve um erro.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
    }

    public function microStrategy()
    {
        return view('compliance.importacoes.microStrategy');
    }
    // ######################### FIM  Microestrategy #######################

    // ######################### INICIO SGDO DISTRIBUIÇÃO  ########
    public function exportSgdoDistribuicao() {
        return Excel::download(new ExportSgdoDistribuicao, 'sgdoDistribuicao.xlsx');
    }
    public function importSgdoDistribuicao(Request $request) {
        $row = 0;
        $dt_job = Carbon::now();
        $dtmenos180dias = Carbon::now();
        $dtmenos180dias = $dtmenos180dias->subDays(180);

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx'
        ]);

//        $size = $request->file('file')->getSize();
//        dd($size);

        if($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $size = $request->file('file')->getSize()/1024;

            if ($size > 6000){
                \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande.'
                    , 'class' => 'red white-text']);
                return redirect()->route('importacao');
            }
            else{
                $sgdoDistribuicao = Excel::toArray(new ImportSgdoDistribuicao, request()->file('file'));
            }

            Try{
                //  php artisan queue:work --queue=importacao
                $job = (new JobSgdoDistribuicao( $sgdoDistribuicao, $dt_job, $dtmenos180dias ))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'SgdoDistribuicao, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            }
            catch (Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'SgdoDistribuicao, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'SgdoDistribuicao, não pode ser importado Erro: '.$e->getCode().''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }


        }

        else{

            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function sgdoDistribuicao() {
        return view('compliance.importacoes.sgdoDistribuicao');  //
    }
    // ######################### FIM SGDO DISTRIBUICAO ############

    // ######################### INICIO Controle de Viagem  Embarque Desembarque ###############
    public function exportControleDeViagem()
    {
        return Excel::download(new ExportControleDeViagem, 'ControleDeViagem.xlsx');
    }
    public function importControleDeViagem(Request $request)
    {
        $row = 0;
        $dt_job = Carbon::now();
        $dtmenos180dias = Carbon::now();
        $dtmenos180dias = $dtmenos180dias->subDays(180);
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if(empty($request->file('file'))) {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser 276-1-ControleDeViagem.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes()) {

            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $controle_de_viagens = Excel::toArray(new ImportControleDeViagem,  request()->file('file'));

            Try{
                //  php artisan queue:work --queue=importacao
                $job = (new JobControleViagem( $controle_de_viagens, $dt_job, $dtmenos180dias ))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobControleViagem, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            }
            catch (Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'JobControleViagem, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'JobControleViagem, não pode ser importado Erro: '.$e->getCode().''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }
        }
        else
        {
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function controleDeViagem()
    {
        return view('compliance.importacoes.controleDeViagem');  //
    }
    // ######################### FIM Controle de Viagem Embarque Desembarque ###################


     ######################### INICIO Controle de Viagem  Apontamento ###############
    public function exportApontamentoCV()
    {
        return Excel::download(new ExportControleDeViagemApontamentos, 'ControleDeViagemApontamento.xlsx');
    }
    public function importApontamentoCV(Request $request)
    {
        $dt_job = Carbon::now();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;
        $extension = $request->file('file')->getClientOriginalExtension();

        if (($size > 500) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
//        dd($size, $extencion);
        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $controle_de_viagens = Excel::toArray(new ImportControleDeViagemApontamentos,  request()->file('file'));

            try {
                //  php artisan queue:work --queue=importacao
                $job = (new JobApontamentoCV($controle_de_viagens, $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);



                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobApontamentoCV, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobApontamentoCV, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobApontamentoCV, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode ser importado formato inválido '. $extension .' deve ser (xlsx).'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

    }

    public function apontamentoCV()
    {
        return view('compliance.importacoes.apontamentoCV');  //
    }
    // ######################### FIM Controle de Viagem Apontamento ###################


    // ######################### INICIO plp pendente  ###############
    public function exportPLPListaPendente()
    {
        return Excel::download(new ExportPLPListaPendente, 'PLPListaPendente.xlsx');
    }
    public function importPLPListaPendente(Request $request)
    {
        $row = 0;
        $dt_job = Carbon::now();

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if(empty($request->file('file')))
        {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser 274-1-PLP-ListasPendentes.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $plpListaPendentes = Excel::toArray(new ImportPLPListaPendente,  request()->file('file'));

            Try{
                //  php artisan queue:work --queue=importacao
                $job = (new JobPLPpendente( $plpListaPendentes, $dt_job ))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobPLPpendente, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            }
            catch (Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'JobPLPpendente, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'JobPLPpendente, não pode ser importado Erro: '.$e->getCode().''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }


        }else{
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function plpListaPendente()
    {
        return view('compliance.importacoes.plpListaPendente');  //
    }
    // ######################### FIM plp pendentes #################

    // ######################### INICIO FERIAS POR MCU #############
    public function exportFerias()
    {
        return Excel::download(new ExportFerias, 'ferias.xlsx');
    }
    public function importFerias(Request $request)
    {
        $row = 0;
        $dt_job = Carbon::now();
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx'
        ]);

        if(empty($request->file('file'))) {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser 272-3-WebSGQ3 - Fruicao de ferias por MCU.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }
        if($validator->passes()) {

            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $ferias = Excel::toArray(new ImportFeriasPorMcu,  request()->file('file'));

            Try{
                //  php artisan queue:work --queue=importacao
                $job = (new JobFeriasPorMCU( $ferias, $dt_job ))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobFeriasPorMCU - Férias por MCU, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            }
            catch (Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'JobFeriasPorMCU - Férias por MCU,, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'JobFeriasPorMCU - Férias por MCU, não pode ser importado Erro: '.$e->getCode().''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }


        }else{
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function ferias()
    {
        return view('compliance.importacoes.ferias');  //
    }
    // ######################### FIM FERIAS POR MCU #################

    // ######################### INICIO CFTV  #######################
    public function exportCftv() {
        return Excel::download(new ExportCftv, 'cftvs.xlsx');
    }
    public function importCftv(Request $request) {

//         Amilton, fazer um crud para manter o cadastro de CFTV  e conceder a permissão para as CSEPS
//    o botão de chamada já está previsto no cadastro de unidades, porém não está implementado. sendo o acesso por CSEPs o usuário vai ver apenas suas unidades.

        $row = 0;
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if(empty($request->file('file'))){
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser 272-4-SEGURANÇA-Monitoramento-CFTV.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes())
        {
            $cftvs = Excel::toArray(new ImportCftv,  request()->file('file'));


            foreach($cftvs as $dados)
            {
                foreach($dados as $registro) {
                    if(!empty($registro['data_ultima_conexao']))
                    {
                        try
                        {
                            $data_ultima_conexao = $this->transformDate($registro['data_ultima_conexao']);
                        }
                        catch (Exception $e) {
                            $data_ultima_conexao       = "";
                        }
                    }
                    $cftv = Cftv::firstOrNew(
                        [
                            'mcu' => $registro['mcu']
                            ,'end_ip' => $registro['end_ip']
                        ],
                        [
                            'cameras_fixa_cf' => $registro['cameras_fixa_cf']
                            ,'mcu' => $registro['mcu']
                            ,'unidade' => $registro['unidade']
                            , 'cameras_infra_vermelho_cir' => $registro['cameras_infra_vermelho_cir']
                            , 'dome' => $registro['dome']
                            , 'modulo_dvr' => $registro['modulo_dvr']
                            , 'no_break' => $registro['no_break']
                            , 'hack' => $registro['hack']
                            , 'pc_auxiliar' => $registro['pc_auxiliar']
                            , 'portaweb' => $registro['portaweb']
                            , 'end_ip' => $registro['end_ip']
                            , 'link' => $registro['link']
                            , 'user' => $registro['user']
                            , 'password' => $registro['password']
                            , 'port' => $registro['port']
                            , 'marcamodelo' => $registro['marcamodelo']
                            , 'statusconexao' => $registro['statusconexao']
                            , 'observacao' => $registro['observacao']
                            , 'data_ultima_conexao' => $data_ultima_conexao
                        ]);
                    $cftv ->save();
                    $row ++;
                }
            }
            \Session::flash('mensagem',['msg'=>'O Arquivo subiu com '.$row.' linhas Corretamente'
                ,'class'=>'green white-text']);
            return redirect()->route('importacao');
        }else{
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function cftv() {
        return view('compliance.importacoes.cftv');  //
    }
    // ######################### FIM CFTV #######################

    // ########## INICIO ABSENTEISMO MCU   Frequencia por SE ##
    public function exportAbsenteismo() //Frequencia por SE
    {
        return Excel::download(new ExportAbsenteismo, 'absenteismo.xlsx');
    }
    public function importAbsenteismo(Request $request)
    {
        $row = 0;
        $dt_job = Carbon::now();
        $dtmenos12meses = Carbon::now();
        $dtmenos12meses = $dtmenos12meses->subMonth(12);

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls'
        ]);
        if(empty($request->file('file')))
        {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
        O Arquivo de ser 272-3-WebSGQ3 - Frequencia por SE.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes())
        {

            //  php artisan queue:work --queue=importacao
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);
            $absenteismos = Excel::toArray(new ImportAbsenteismo,  request()->file('file'));

//            dd($absenteismos, $dtmenos12meses, $dt_job );

            Try{
                $job = (new JobAbsenteismo( $absenteismos, $dt_job, $dtmenos12meses))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));

                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobAbsenteismo - Frequencia por SE, aguardando processamento.'
                    , 'class' => 'blue white-text']);

                return redirect()->route('importacao');
            }
            catch (\Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'JobAbsenteismo - Frequencia por SE, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'JobAbsenteismo - Frequencia por SE, não pode ser importado Erro: '.$e->getCode().''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }
        }
        else{
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function absenteismo() {
        return view('compliance.importacoes.absenteismo');  //
    }
    // #################### FIM ABSENTEISMO  Frequencia por SE ##

    // ######################### INICIO ALARMES #####################
    public function exportAlarme()
    {
        return Excel::download(new ExportAlarmes, 'alarme.xlsx');
    }
    public function importAlarme(Request $request)
    {
        $row = 0;
        $dtmenos12meses = Carbon::now();
        $dt_job = Carbon::now();
        $dtmenos12meses = $dtmenos12meses->subMonth(12);

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx'
        ]);

        if(empty($request->file('file')))
        {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser 272-2-SEGURANÇA-SistemaMonitoramento_ALARMES *.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes())
        {

            //  php artisan queue:work --queue=importacao
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $alarmes = Excel::toArray(new ImportAlarmes,  request()->file('file'));

            Try{
                $job = (new JobAlarmes( $alarmes, $dt_job, $dtmenos12meses))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobAlarmes, aguardando processamento.'
                    , 'class' => 'blue white-text']);

                return redirect()->route('importacao');
            } catch (\Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'JobAlarmes, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'JobAlarmes, não pode ser importado Erro: '.$e->getCode().''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }else{
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function alarme()
    {
        return view('compliance.importacoes.alarme');  //
    }
    /// ######################### FIM ALARMES #######################

    /// ######################### BLOCO  RespDefinida  nao tem job  aguardando definição com o 01-04=2020 departamento ###################
    public function exportRespDefinida()
    {
        return Excel::download(new ExportRespDefinida, 'RespDefinida.xlsx');
    }
    public function importRespDefinida(Request $request)
    {
        $row = 0;

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if($request->file('file') == "") {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser  271-1-SEGURANÇA-POSTAL-PendênciasApuracaoRespDefinida.xls! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes())
        {
            $RespDefinidas = Excel::toArray(new ImportRespDefinida,  request()->file('file'));
            foreach($RespDefinidas as $registros)
            {
                foreach($registros as $dado)
                {
                    $res = DB::table('resp_definidas')
                        ->where('objeto', '=',  $dado['objeto'])
                        ->select(
                            'resp_definidas.id'
                        )
                        ->first();
                    if(!empty(  $res->id ))
                    {
                        $registro = RespDefinida::find($res->id);
                    }
                    else
                    {
                        $registro = new RespDefinida;
                    }
                    $registro->unidade      = $dado['unidade'];
                    if(!empty($dado['data_pagamento']))
                    {
                        try {
                            $dt = $this->transformDate(strtr($dado['data_pagamento'], '/', '-'));
                            $registro->data_pagamento = $dt;
                        }
                        catch (Exception $e)
                        {
                            $registro->data_pagamento = null;
                        }
                    }
                    $registro->objeto      = $dado['objeto'];
                    if(!empty($dado['datapostagem']))
                    {
                        try {
                            $dt = $this->transformDate(strtr($dado['datapostagem'], '/', '-'));
                            $registro->datapostagem         = $dt;
                        }  catch (Exception $e) {
                            $registro->datapostagem         = null;
                        }
                    }
                    $registro->servico_produto      = $dado['servico_produto'];

                    if($dado['valor_da_indenizacao'] != 0)
                    {
                        $registro->valor_da_indenizacao = str_replace(",", ".", $dado['valor_da_indenizacao']);
                    }
                    $registro->sto      = $dado['sto'];
                    $registro->mcu      = $dado['mcu'];
                    $registro->subordinacao      = $dado['subordinacao'];
                    $registro->nu_pedidoinformacao      = $dado['nu_pedidoinformacao'];
                    $registro->se_pagadora      = $dado['se_pagadora'];
                    if(!empty($dado['data']))
                    {
                        try
                        {
                            $dt = $this->transformDate(strtr($dado['data'], '/', '-'));
                            $registro->data         = $dt;
                        }
                        catch (Exception $e)
                        {
                            $registro->data         = null;
                        }
                    }
                    $registro->nu_sei      = $dado['nu_sei'];
                    $registro->nu_sei_abertounidade      = $dado['nu_sei_abertounidade'];
                    $registro->situacao      = $dado['situacao'];
                    $registro->empregadoresponsavel      = $dado['empregadoresponsavel'];
                    $registro->observacoes      = $dado['observacoes'];
                    $registro->conclusao      = $dado['conclusao'];
                    $registro->providenciaadotada      = $dado['providenciaadotada'];
                    $registro->save();
                    $row ++;
                }
            }

            \Session::flash('mensagem',['msg'=>'O Arquivo subiu com '.$row.' linhas Corretamente'
                ,'class'=>'green white-text']);
            return redirect()->route('importacao');
        }else {
            \Session::flash('mensagem',['msg'=>'Registros Responsabilidade Definida  Não pôde ser importado! Tente novamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

    }
    public function RespDefinida()
    {
        return view('compliance.importacoes.respDefinida');  //
    }
    /// ######################### FIM RespDefinida #######################

    /// ######################### BLOCO  SL02 BDF #####################
    /// este arquivo não é mais do bdf veja na pasta o lay out correto de outro sistema.
    public function exportSL02_bdf() {
        return Excel::download(new ExportSL02_bdf, 'SL02_bdf.xlsx');
    }
    public function importSL02_bdf(Request $request) {
        $row = 0;
        $dtmenos120dias = Carbon::now();
        $dtmenos120dias = $dtmenos120dias->subDays(120);

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if($request->file('file') == "") {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser  270-4-FINANCEIRO-SLD02_BDF_LimiteEncaixe.xls! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes()) {

            $dt_job = Carbon::now();
            $row =0;
            $dtmenos150dias = $dt_job->subDays(150);

            $SL02_bdfs = Excel::toArray(new ImportSL02_bdf,  request()->file('file'));
            //  php artisan queue:work --queue=importacao
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            Try{
                $job = (new JobSLD_02_BDF( $SL02_bdfs, $dt_job, $dtmenos150dias))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                \Session::flash('mensagem', ['msg' => 'JobSLD_02_BDF, aguardando processamento.'
                    , 'class' => 'blue white-text']);

                return redirect()->route('importacao');
            } catch (\Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'JobSLD_02_BDF, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'JobSLD_02_BDF, não pode ser importado Erro: '.$e->getCode().''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        } else {
            \Session::flash('mensagem',['msg'=>'Registros SL02_bdf Não pôde ser importado! Tente novamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }
    }
    public function SL02_bdf()
    {
        return view('compliance.importacoes.sld02_bdf');  //
    }
    /// ######################### FIM SL02 BDF ########################

    /// ######################### BLOCO  SMB BDF #######################
    public function exportSmb_bdf() {
        return Excel::download(new ExportSMBxBDF_NaoConciliado, 'SMBxBDF_NaoConciliado.xlsx');
    }

    public function importSmb_bdf(Request $request) {

        $dtmenos120dias = Carbon::now();
        $dtmenos120dias = $dtmenos120dias->subDays(120);
        $dt_job = Carbon::now();
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;
        $extencion = $request->file('file')->getClientOriginalExtension();

        if (($size > 500) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
//dd($extencion);

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $SMBxBDF_NaoConciliado = Excel::toArray(new ImportSMBxBDF_NaoConciliado,  request()->file('file'));
            try {
                //  php artisan queue:work --queue=importacao
                $job = (new JobSMBxBDF($SMBxBDF_NaoConciliado,$dtmenos120dias, $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobSMBxBDF, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobSMBxBDF, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobSMBxBDF, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode ser importado formato inválido '. $extencion .' deve ser (xlsx).'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

    }

    public function smb_bdf() {
        return view('compliance.importacoes.smb_bdf');  //
    }
    /// ######################### FIM SMB BDF #######################

    /// ######################### BLOCO  PROTER#######################
    public function exportProter()
    {
        return Excel::download(new ExportProter, 'proters.xlsx');
    }
    public function importProter(Request $request) {
        $row=0;
        $mcuanterior=0;
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if($request->file('file') == "")
        {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser 270-2-FINANCEIRO-Proter_ProtecaoReceita.xls! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }
        if($validator->passes())
        {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $proters = Excel::toArray(new ImportProter,  request()->file('file'));
            $dt_job = Carbon::now();

            ini_set('memory_limit', '128M');
// ############### API carbon não está funcionando CORRETAMENTE com JOBs.
//  php artisan queue:work --queue=importacao
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);
            Try{

                $job = (new JobProter( $proters, $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                \Session::flash('mensagem', ['msg' => 'JobProter, aguardando processamento.'
                    , 'class' => 'blue white-text']);

                return redirect()->route('importacao');
            } catch (\Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'JobProter, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: '.$e->getCode(), 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'JobProter, não pode ser importado Erro: '.$e->getCode().'.'
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

//          DB::table('proters')->truncate(); //excluir e zerar a tabela


        }
        else  {
            \Session::flash('mensagem',['msg'=>'Registros PROTER Não pôde ser importado! Tente novamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }
    }
    public function proter()
    {
        return view('compliance.importacoes.proter');  //
    }
    /// ######################### FIM PROTER #######################

    /// ######################### BLOCO  DEBITO EMPREGADO ##########
    public function exportDebitoEmpregados()
    {
        return Excel::download(new ExportDebitoEmpregados, 'debitoEmpregados.xlsx');
    }
    public function importDebitoEmpregados(Request $request)
    {
        $row=0;
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if($request->file('file') == "")
        {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser  270-1-FINANCEIRO-WebCont_DebitoEmpregado.xlsx! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }
        if($validator->passes())
        {
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);
            ini_set('memory_limit', '512M');
            $debitoEmpregados = Excel::toArray(new ImportDebitoEmpregados,  request()->file('file'));
            $dt_job = Carbon::now();

            Try{
                $job = (new JobWebCont( $debitoEmpregados , $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                \Session::flash('mensagem', ['msg' => 'Job JobWebCont, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }
            catch (\Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => ' Job JobWebCont, tente uma quantidade menor de registros.
                    Tente um arquivo de aproximadamente 4.00kb. Erro'. $e->getCode() , 'class' => 'red white-text']);
                }
                else {
                    \Session::flash('mensagem', ['msg' => 'Job JobWebCont, não pode ser importado. Erro '. $e->getCode() , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }else{
            \Session::flash('mensagem',['msg'=>'Registros WebCont Não pôde ser importado! Tente novamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }
    }
    public function debitoEmpregados()
    {
        return view('compliance.importacoes.debitoEmpregado');  //
    }
    /// ######################### FIM DEBITO EMPREGADO #############

   // ######################### INICIO FERIADOS ######################
    public function exportFeriado()
    {
        return Excel::download(new ExportFeriado, 'feriados.xlsx');
    }
    public function importFeriado(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if(empty($request->file('file')))
        {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser feriados.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes())
        {
            $feriados = Excel::toArray(new ImportFeriado,  request()->file('file'));
          //  DB::table('feriados')->truncate(); //excluir e zerar a tabela
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);
            foreach($feriados as $dados)
            {
                $row = 0;
                foreach($dados as $registro)
                {
                    $cad = DB::table('feriados')
                        ->where('uf', '=',  $registro['uf'])
                        ->where('nome_municipio', '=',  $registro['nome_municipio'])
                        ->where('tipo_feriado', '=',  $registro['tipo_feriado'])
                        ->where('data_do_feriado', '=', $this->transformDateMesDia($registro['data_do_feriado']))
                        ->select(
                            'feriados.id'
                        )
                        ->first();

                    if(!empty(  $cad->id ))
                    {
                        $feriado = Feriado::find($cad->id);
                        $feriado->uf      = $registro['uf'];
                        $feriado->nome_municipio  = $registro['nome_municipio'];
                        $feriado->tipo_feriado  = $registro['tipo_feriado'];
                        $feriado->descricao_feriado  = $registro['descricao_feriado'];
                        $dt         = $this->transformDateMesDia($registro['data_do_feriado']);
                        $feriado->data_do_feriado         = $dt;
                    }
                    else
                    {
                        $feriado = new Feriado;
                        $feriado->uf      = $registro['uf'];
                        $feriado->nome_municipio  = $registro['nome_municipio'];
                        $feriado->tipo_feriado  = $registro['tipo_feriado'];
                        $feriado->descricao_feriado  = $registro['descricao_feriado'];
                        $dt         = $this->transformDateMesDia($registro['data_do_feriado']);
                        $feriado->data_do_feriado         = $dt;
                    }
                    $feriado ->save();
                    $row ++;
                }
            }
            \Session::flash('mensagem',['msg'=>'O Arquivo subiu com '.$row.' linhas Corretamente'
                ,'class'=>'green white-text']);
            return redirect()->route('importacao');
        }else{
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function feriado()
    {
        return view('compliance.importacoes.feriado');  //
    }
    /// ######################### FIM FERIADOS #######################

    /// ######################### BLOCO  CADASTRAL ###################
    public function exportCadastral()
    {
        return Excel::download(new ExportCadastral, 'cadastrals.xlsx');
    }
    public function importCadastral(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx'
        ]);
        if($request->file('file') == "")
        {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser  WebSGQ 3 - Efetivo analitico por MCU.xlsx! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }
        if($validator->passes())
        {
       //     DB::table('cadastral')->truncate(); //excluir dados e zerar a tabela
            ini_set('memory_limit', '512M');
            $cadastrals = Excel::toArray(new ImportCadastral,  request()->file('file'));
            $dt = Carbon::now();

            try {
                $job = (new JobCadastral($cadastrals, $dt ))
                    ->onQueue('importacao')
                    ->delay($dt->addMinutes(1));
                dispatch($job);
            } catch (\Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'Job Cadastral enorme, tente uma quantidade menor de registros. Tente um arquivo de aproximadamente 4.000kb.'
                        , 'class' => 'red white-text']);

                } else {
                    \Session::flash('mensagem', ['msg' => 'Job Cadastral não pode ser importado, erro não identificado.'
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '64M');
                return redirect()->route('importacao');

            }
            \Session::flash('mensagem', ['msg' => 'Job Cadastral aguardando processamento.'
                , 'class' => 'blue white-text']);
            ini_set('memory_limit', '64M');

            return redirect()->route('importacao');
        }
    }
    public function cadastral()
    {
        return view('compliance.importacoes.cadastral');
    }
    /// ######################### FIM CADASTRAL ######################

    // ######################### INICIO   SNCI ##################
    public function exportSnci() {
        return Excel::download(new ExportSnci, 'snci.xlsx');
    }
    public function importSnci(Request $request) {

//        DB::table('snci')->truncate();
//        dd('truncou');

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {

            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo. Não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        if ($validator->passes()) {

            ini_set('memory_limit', '512M');
            $snci = Excel::toArray(new ImportSnci, request()->file('file'));
            $dt = Carbon::now();

            Try{
                $job = (new JobSnci( $snci , $dt))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt->addMinutes(1));
                dispatch($job);
            } catch (\Exception $e) {
                if(substr( $e->getCode(), 0, 2) == 'HY'){
                    \Session::flash('mensagem', ['msg' => 'Job SNCI enorme, tente uma quantidade menor de registros. Tente um arquivo de aproximadamente 4.00kb.'
                        , 'class' => 'red white-text']);
                }else {
                    \Session::flash('mensagem', ['msg' => 'Job SNCI não pode ser importado, erro não identificado.'
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '64M');
                return redirect()->route('importacao');
            }
            \Session::flash('mensagem', ['msg' => 'Job Snci aguardando processamento.'
                , 'class' => 'blue white-text']);
            ini_set('memory_limit', '64');
            return redirect()->route('importacao');
        }
    }
    public function snci()
    {
        return view('compliance.importacoes.snci');  //
    }
    // ######################### FIM  SNCI #######################

    // ######################### INICIO IMPORTAR UNIDADES ############
    public function importUnidades(Request $request) {

        //variaveis

        $dt_job = Carbon::now();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;
        $extencion = $request->file('file')->getClientOriginalExtension();

        if (($size > 520) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
//dd($extencion);

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $unidades = Excel::toArray(new ImportUnidades,  request()->file('file'));

            try {
                //  php artisan queue:work --queue=importacao
                $job = (new JobUnidades($unidades, $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobUnidades, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobUnidades, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobUnidades, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode ser importado formato inválido '. $extencion .' deve ser (xlsx).'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

    }
    public function unidades() {
        return view('compliance.importacoes.unidades');  //
    }
    public function importAdicionalUnidades(Request $request) {

 //variaveis

        $dt_job = Carbon::now();
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);

        if (empty($request->file('file'))) {
            \Session::flash('mensagem', ['msg' => 'Erro o Arquivo não foi Selecionado.'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

        $size = $request->file('file')->getSize() / 1024;
        $extencion = $request->file('file')->getClientOriginalExtension();

        if (($size > 520) || ($size == 0)) {
            \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. '
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }
//dd($extencion);

        if ($validator->passes()) {
            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);

            $unidades = Excel::toArray(new ImportUnidades,  request()->file('file'));

            try {
                //  php artisan queue:work --queue=importacao

                $job = (new JobHorariosDERAT($unidades, $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);



                foreach($unidades as $dados) {
                    foreach($dados as $registro) {

                        //trata hora  horario_inicio_expediente
                        if(! Empty($registro['horario_inicio_expediente'])) {
                            try {
                                $dmt_number = floatVal($registro['horario_inicio_expediente']);
                                $horario_inicio_expediente = 1440 * $dmt_number / 60 ;  //15.33;
                                $horario_inicio_expediente = sprintf('%02d:%02d', (int) $horario_inicio_expediente, fmod($horario_inicio_expediente, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $horario_inicio_expediente    = null;
                            }
                        }
                        else {
                            $horario_inicio_expediente    = null;
                        }

                        //trata hora  horario_fim_expediente
                        if(! Empty($registro['horario_fim_expediente'])) {
                            try {
                                $dmt_number = floatVal($registro['horario_fim_expediente']);
                                $horario_fim_expediente = 1440 * $dmt_number / 60 ;  //15.33;
                                $horario_fim_expediente = sprintf('%02d:%02d', (int) $horario_fim_expediente, fmod($horario_fim_expediente, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $horario_fim_expediente    = null;
                            }
                        }
                        else {
                            $horario_fim_expediente    = null;
                        }

                        //trata hora  horario_inicio_almoco
                        if(! Empty($registro['horario_inicio_almoco'])) {
                            try {
                                $dmt_number = floatVal($registro['horario_inicio_almoco']);
                                $horario_inicio_almoco = 1440 * $dmt_number / 60 ;  //15.33;
                                $horario_inicio_almoco = sprintf('%02d:%02d', (int) $horario_inicio_almoco, fmod($horario_inicio_almoco, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $horario_inicio_almoco    = null;
                            }
                        }
                        else {
                            $horario_inicio_almoco    = null;
                        }

//trata hora  horario_fim_almoco
                        if(! Empty($registro['horario_fim_almoco'])) {
                            try {
                                $dmt_number = floatVal($registro['horario_fim_almoco']);
                                $horario_fim_almoco = 1440 * $dmt_number / 60 ;  //15.33;
                                $horario_fim_almoco = sprintf('%02d:%02d', (int) $horario_fim_almoco, fmod($horario_fim_almoco, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $horario_fim_almoco    = null;
                            }
                        }
                        else {
                            $horario_fim_almoco    = null;
                        }

//trata hora  inicio_funcionamento_sabado
                        if(! Empty($registro['inicio_funcionamento_sabado'])) {
                            try {
                                $dmt_number = floatVal($registro['inicio_funcionamento_sabado']);
                                $inicio_funcionamento_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                                $inicio_funcionamento_sabado = sprintf('%02d:%02d', (int) $inicio_funcionamento_sabado, fmod($inicio_funcionamento_sabado, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $inicio_funcionamento_sabado    = null;
                            }
                        }
                        else {
                            $inicio_funcionamento_sabado    = null;
                        }

                        //trata hora  fim_funcionamento_sabado
                        if(! Empty($registro['fim_funcionamento_sabado'])) {
                            try {
                                $dmt_number = floatVal($registro['fim_funcionamento_sabado']);
                                $fim_funcionamento_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                                $fim_funcionamento_sabado = sprintf('%02d:%02d', (int) $fim_funcionamento_sabado, fmod($fim_funcionamento_sabado, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $fim_funcionamento_sabado    = null;
                            }
                        }
                        else {
                            $fim_funcionamento_sabado    = null;
                        }

//trata hora  inicio_funcionamento_domingo
                        if(! Empty($registro['inicio_funcionamento_domingo'])) {
                            try {
                                $dmt_number = floatVal($registro['inicio_funcionamento_domingo']);
                                $inicio_funcionamento_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                                $inicio_funcionamento_domingo = sprintf('%02d:%02d', (int) $inicio_funcionamento_domingo, fmod($inicio_funcionamento_domingo, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $inicio_funcionamento_domingo    = null;
                            }
                        }
                        else {
                            $inicio_funcionamento_domingo    = null;
                        }

// trata hora  fim_funcionamento_domingo
                        if(! Empty($registro['fim_funcionamento_domingo'])) {
                            try {
                                $dmt_number = floatVal($registro['fim_funcionamento_domingo']);
                                $fim_funcionamento_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                                $fim_funcionamento_domingo = sprintf('%02d:%02d', (int) $fim_funcionamento_domingo, fmod($fim_funcionamento_domingo, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $fim_funcionamento_domingo    = null;
                            }
                        }
                        else {
                            $fim_funcionamento_domingo    = null;
                        }

//trata hora  inicio_plantao_sabado
                        if(! Empty($registro['inicio_plantao_sabado'])) {
                            try {
                                $dmt_number = floatVal($registro['inicio_plantao_sabado']);
                                $inicio_plantao_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                                $inicio_plantao_sabado = sprintf('%02d:%02d', (int) $inicio_plantao_sabado, fmod($inicio_plantao_sabado, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $inicio_plantao_sabado    = null;
                            }
                        }
                        else {
                            $inicio_plantao_sabado    = null;
                        }

//trata hora  fim_plantao_sabado
                        if(! Empty($registro['fim_plantao_sabado'])) {
                            try {
                                $dmt_number = floatVal($registro['fim_plantao_sabado']);
                                $fim_plantao_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                                $fim_plantao_sabado = sprintf('%02d:%02d', (int) $fim_plantao_sabado, fmod($fim_plantao_sabado, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $fim_plantao_sabado    = null;
                            }
                        }
                        else {
                            $fim_plantao_sabado    = null;
                        }

//trata hora  inicio_plantao_domingo
                        if(! Empty($registro['inicio_plantao_domingo'])) {
                            try {
                                $dmt_number = floatVal($registro['inicio_plantao_domingo']);
                                $inicio_plantao_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                                $inicio_plantao_domingo = sprintf('%02d:%02d', (int) $inicio_plantao_domingo, fmod($inicio_plantao_domingo, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $inicio_plantao_domingo    = null;
                            }
                        }
                        else {
                            $inicio_plantao_domingo    = null;
                        }

//trata hora  fim_plantao_domingo
                        if(! Empty($registro['fim_plantao_domingo'])) {
                            try {
                                $dmt_number = floatVal($registro['fim_plantao_domingo']);
                                $fim_plantao_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                                $fim_plantao_domingo = sprintf('%02d:%02d', (int) $fim_plantao_domingo, fmod($fim_plantao_domingo, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $fim_plantao_domingo    = null;
                            }
                        }
                        else {
                            $fim_plantao_domingo    = null;
                        }

//trata hora  horario_inicio_distribuicao
                        if(! Empty($registro['horario_inicio_distribuicao'])) {
                            try {
                                $dmt_number = floatVal($registro['horario_inicio_distribuicao']);
                                $horario_inicio_distribuicao = 1440 * $dmt_number / 60 ;  //15.33;
                                $horario_inicio_distribuicao = sprintf('%02d:%02d', (int) $horario_inicio_distribuicao, fmod($horario_inicio_distribuicao, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $horario_inicio_distribuicao    = null;
                            }
                        }
                        else {
                            $horario_inicio_distribuicao    = null;
                        }

//trata hora  horario_fim_distribuicao
                        if(! Empty($registro['horario_fim_distribuicao'])) {
                            try {
                                $dmt_number = floatVal($registro['horario_fim_distribuicao']);
                                $horario_fim_distribuicao = 1440 * $dmt_number / 60 ;  //15.33;
                                $horario_fim_distribuicao = sprintf('%02d:%02d', (int) $horario_fim_distribuicao, fmod($horario_fim_distribuicao, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $horario_fim_distribuicao    = null;
                            }
                        }
                        else {
                            $horario_fim_distribuicao    = null;
                        }

//trata hora  dh_semana
                        if(! Empty($registro['dh_semana'])) {
                            try {
                                $dmt_number = floatVal($registro['dh_semana']);
                                $dh_semana = 1440 * $dmt_number / 60 ;  //15.33;
                                $dh_semana = sprintf('%02d:%02d', (int) $dh_semana, fmod($dh_semana, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $dh_semana    = null;
                            }
                        }
                        else {
                            $dh_semana    = null;
                        }

//trata hora  dh_fim_semana
                        if(! Empty($registro['dh_fim_semana'])) {
                            try {
                                $dmt_number = floatVal($registro['dh_fim_semana']);
                                $dh_fim_semana = 1440 * $dmt_number / 60 ;  //15.33;
                                $dh_fim_semana = sprintf('%02d:%02d', (int) $dh_fim_semana, fmod($dh_fim_semana, 1) * 60).':00'; // 15:19
                            }
                            catch (\Exception $e) {
                                $dh_fim_semana    = null;
                            }
                        }
                        else {
                            $dh_fim_semana    = null;
                        }


                        if($registro['funciona_sabado']!=='Sim') {
                            $trabalha_sabado='Não';
                        }
                        if($registro['funciona_domingo']!=='Sim') {
                            $trabalha_domingo='Não';
                        }
                        if($registro['tem_plantao']!=='Sim') {
                            $tem_plantao='Não';
                        }
                        if($registro['tem_plantao']!=='Sim') {
                            $tem_distribuicao='tem_distribuicao';
                        }

                        $res = DB::table('unidades')
                            ->where('an8', '=',  (int)$registro['no_cad_geral'])
                            ->select(
                                'unidades.*'
                            )
                            ->first();
//                        dd('aki', $res);
                        if(!empty(  $res->id )) {
                            $unidade = Unidade::find($res->id);
                            $unidade->inicio_atendimento = $horario_inicio_expediente;
                            $unidade->final_atendimento = $horario_fim_expediente;
                            $unidade->inicio_expediente = $horario_inicio_expediente;
                            $unidade->final_expediente = $horario_fim_expediente;
                            $unidade->inicio_intervalo_refeicao = $horario_inicio_almoco;
                            $unidade->final_intervalo_refeicao = $horario_fim_almoco;
                            $unidade->trabalha_sabado =$trabalha_sabado;
                            $unidade->trabalha_domingo =$trabalha_domingo;
                            $unidade->tem_plantao = $tem_plantao;
                            $unidade->tem_distribuicao = $tem_distribuicao;
                            $unidade->inicio_expediente_sabado = $inicio_funcionamento_sabado;
                            $unidade->final_expediente_sabado = $fim_funcionamento_sabado;
                            $unidade->inicio_expediente_domingo = $inicio_funcionamento_domingo;
                            $unidade->final_expediente_domingo = $fim_funcionamento_domingo;
                            $unidade->inicio_plantao_sabado = $inicio_plantao_sabado;
                            $unidade->final_plantao_sabado = $fim_plantao_sabado;
                            $unidade->inicio_plantao_domingo = $inicio_plantao_domingo;
                            $unidade->final_plantao_domingo = $fim_plantao_domingo;
                            $unidade->inicio_distribuicao = $horario_inicio_distribuicao;
                            $unidade->final_distribuicao = $horario_fim_distribuicao;
                            $unidade->horario_lim_post_na_semana = $dh_semana;
                            $unidade->horario_lim_post_final_semana = $dh_fim_semana;
                            $unidade->update();
//                                                   dd('aki', $unidade);
                        }
                    }
                }

                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);

                \Session::flash('mensagem', ['msg' => 'JobHorariosDERAT, aguardando processamento.'
                    , 'class' => 'blue white-text']);
                return redirect()->route('importacao');
            } catch (Exception $e) {
                if (substr($e->getCode(), 0, 2) == 'HY') {
                    \Session::flash('mensagem', ['msg' => 'JobHorariosDERAT, tente uma quantidade menor
                           de registros. Tente um arquivo de aproximadamente 4.00kb. Erro: ' . $e->getCode(), 'class' => 'red white-text']);
                } else {
                    \Session::flash('mensagem', ['msg' => 'JobHorariosDERAT, não pode ser importado Erro: ' . $e->getCode() . ''
                        , 'class' => 'red white-text']);
                }
                ini_set('memory_limit', '128');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->route('importacao');
            }

        }
        else {
            \Session::flash('mensagem', ['msg' => 'Arquivo não pode ser importado formato inválido '. $extencion .' deve ser (xlsx).'
                , 'class' => 'red white-text']);
            return redirect()->route('importacao');
        }

    }

    public function unidadesAdicional() {

        return view('compliance.importacoes.unidadesAdicional');  //
    }

    // ######################### FIM IMPORTAR UNIDADES ###############


    public function transformDate($value, $format = 'Y-m-d')
    {
        try
        {
            return \Carbon\Carbon::instance(
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\ErrorException $e)
        {
             return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
    public function transformTime($value, $format = 'H:i:s')
    {
        try
        {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\ErrorException $e)
        {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    public function transformDateTime($value, $format = 'Y-m-d H:i:s')
    {
        try
        {
            return \Carbon\Carbon::instance(
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\ErrorException $e)
        {
             return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
    public function transformHoraMin($value, $format = 'H:i')
    {
        try
        {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\ErrorException $e)
        {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
    public function transformDayOfWeek($value, $format = 'd')
    {
        try
        {
        return \Carbon\Carbon::instance(
        \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\ErrorException $e)
        {
        return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
    public function transformDateMesDia($value, $format = 'm-d')
    {
        try
        {
            return \Carbon\Carbon::instance(
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\ErrorException $e)
        {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
    public function index()
    {
        return view('compliance.importacoes.index');  //
    }
    function deixarNumero($string){
        return preg_replace("/[^0-9]/", "", $string);
    }
    public function handle()
    {
        \DB::listen(function($res) {
            var_dump($res->sql);
        });
    }
}
