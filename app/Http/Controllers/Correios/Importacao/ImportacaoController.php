<?php

namespace App\Http\Controllers\Correios\Importacao;

use App\Http\Controllers\Controller;

use App\Jobs\JobSLD_02_BDF;
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

//   ##########################################

use App\Imports\ImportControleDeViagemApontamentos;
//use App\Exports\ExportControleDeViagem;
use App\Models\Correios\ModelsAuxiliares\ApontamentoCV;
use App\Jobs\JobApontamentoCV;


use App\Models\Correios\Unidade;
use App\Imports\ImportUnidades;
use App\Models\Correios\UnidadeEndereco;


use App\Models\Correios\ModelsAuxiliares\Evento;
use App\Imports\ImportEventos;
use App\Exports\ExportEventos;

use App\Models\Correios\ModelsAuxiliares\RespDefinida;
use App\Imports\ImportRespDefinida;
use App\Exports\ExportRespDefinida;

use App\Models\Correios\ModelsAuxiliares\Feriado;
use App\Imports\ImportFeriado;
use App\Exports\ExportFeriado;

use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use App\Imports\ImportSL02_bdf;
use App\Exports\ExportSL02_bdf;

use App\Models\Correios\ModelsAuxiliares\SMBxBDF_NaoConciliado;
use App\Imports\ImportSMBxBDF_NaoConciliado;
use App\Exports\ExportSMBxBDF_NaoConciliado;


use App\Models\Correios\ModelsAuxiliares\Cftv;
use App\Imports\ImportCftv;
use App\Exports\ExportCftv;


//use PhpOffice\PhpSpreadsheet\Calculation\DateTime;


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
                $job = (new JobPagamentosAdicionais($pagamentos_adicionais,$ref, $dt_job))
                    ->onConnection('importacao')
                    ->onQueue('importacao')
                    ->delay($dt_job->addMinutes(1));
                dispatch($job);

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
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if($request->file('file') == "") {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
            O Arquivo de ser  270-3-FINANCEIRO-SMB_ BDF_DepositosNaoConciliados.xls! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        if($validator->passes()) {

            $SMBxBDF_NaoConciliado = Excel::toArray(new ImportSMBxBDF_NaoConciliado,  request()->file('file'));

            foreach($SMBxBDF_NaoConciliado as $registros) {
                foreach($registros as $dado) {

                    $dt         = $this->transformDate($dado['data'])->format('Y-m-d');

                    $dt =   substr(   $dt,0,10);
                    try {
                        $dt         = $this->transformDate($dado['data']);
                    }catch (\Exception $e) {
                        $dt ='';
                    }

                    if($dado['smbdinheiro'] != 0) {
                        $smbdinheiro = str_replace(",", ".", $dado['smbdinheiro']);
                    }
                    else {
                        $smbdinheiro = 0.00;
                    }

                    if($dado['smbcheque'] != 0) {
                        $smbcheque = str_replace(",", ".", $dado['smbcheque']);
                    }
                    else {
                        $smbcheque = 0.00;
                    }

                    if($dado['smbboleto'] != 0) {
                        $smbboleto = str_replace(",", ".", $dado['smbboleto']);
                    }
                    else {
                        $smbboleto = 0.00;
                    }

                    if($dado['smbestorno'] != 0) {
                        $smbestorno = str_replace(",", ".", $dado['smbestorno']);
                    }
                    else {
                        $smbestorno = 0.00;
                    }

                    if($dado['bdfdinheiro'] != 0) {
                        $bdfdinheiro = str_replace(",", ".", $dado['bdfdinheiro']);
                    }
                    else {
                        $bdfdinheiro = 0.00;
                    }

                    if($dado['bdfcheque'] != 0) {
                        $bdfcheque = str_replace(",", ".", $dado['bdfcheque']);
                    }
                    else {
                        $bdfcheque = 0.00;
                    }

                    if($dado['bdfboleto'] != 0) {
                        $bdfboleto = str_replace(",", ".", $dado['bdfboleto']);
                    }
                    else {
                        $bdfboleto = 0.00;
                    }

                    if($dado['divergencia'] != 0) {
                        $divergencia = str_replace(",", ".", $dado['divergencia']);
                    }
                    else {
                        $divergencia = 0.00;
                    }

                    $res = DB::table('smb_bdf_naoconciliados')
                        ->where('mcu', '=',  $dado['mcu'])
                        ->where('data','=', $dt )
                        ->select(
                            'smb_bdf_naoconciliados.id'
                        )
                    ->first();
                    if( ! empty( $res->id )) {
                        $smb_bdf_naoconciliados = SMBxBDF_NaoConciliado::find($res->id);
                        $smb_bdf_naoconciliados->mcu  = $dado['mcu'];
                        $smb_bdf_naoconciliados->agencia      = $dado['agencia'];
                        $smb_bdf_naoconciliados->cnpj  = $dado['cnpj'];
                        $smb_bdf_naoconciliados->status  = $dado['status'];
                        $smb_bdf_naoconciliados->Data = $dt;
                        $smb_bdf_naoconciliados->smbdinheiro = $smbdinheiro;
                        $smb_bdf_naoconciliados->smbcheque = $smbcheque;
                        $smb_bdf_naoconciliados->smbboleto = $smbboleto;
                        $smb_bdf_naoconciliados->smbestorno = $smbestorno;
                        $smb_bdf_naoconciliados->bdfdinheiro = $bdfdinheiro;
                        $smb_bdf_naoconciliados->bdfcheque = $bdfcheque;
                        $smb_bdf_naoconciliados->bdfboleto = $bdfboleto;
                        $smb_bdf_naoconciliados->divergencia = $divergencia;
                    }
                    else {
                        if($dado['status'] == 'Pendente' ) {
                            $smb_bdf_naoconciliados = new SMBxBDF_NaoConciliado;
                            $smb_bdf_naoconciliados->mcu  = $dado['mcu'];
                            $smb_bdf_naoconciliados->agencia      = $dado['agencia'];
                            $smb_bdf_naoconciliados->cnpj  = $dado['cnpj'];
                            $smb_bdf_naoconciliados->status  = $dado['status'];
                            $smb_bdf_naoconciliados->Data = $dt;
                            $smb_bdf_naoconciliados->smbdinheiro = $smbdinheiro;
                            $smb_bdf_naoconciliados->smbcheque = $smbcheque;
                            $smb_bdf_naoconciliados->smbboleto = $smbboleto;
                            $smb_bdf_naoconciliados->smbestorno = $smbestorno;
                            $smb_bdf_naoconciliados->bdfdinheiro = $bdfdinheiro;
                            $smb_bdf_naoconciliados->bdfcheque = $bdfcheque;
                            $smb_bdf_naoconciliados->bdfboleto = $bdfboleto;
                            $smb_bdf_naoconciliados->divergencia = $divergencia;
                        }
                    }
                    $smb_bdf_naoconciliados->save();
//                    $row ++;
                }
            }
            DB::table('smb_bdf_naoconciliados')
                ->where('data', '<', $dtmenos120dias)
            ->delete();

            \Session::flash('mensagem',['msg'=>'O Arquivo subiu corretamente'
                ,'class'=>'green white-text']);

            return redirect()->route('importacao');
        }else{

            \Session::flash('mensagem',['msg'=>'Registros SMBxBDF_NaoConciliado Não pôde ser importado! Tente novamente'
                ,'class'=>'red white-text']);

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

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx'
        ]);

        if(empty($request->file('file'))) {
            \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi Selecionado
                O Arquivo de ser R55001A.xlsx ! Selecione Corretamente'
                ,'class'=>'red white-text']);
            return redirect()->route('importacao');
        }

        ini_set('memory_limit', '512M');
        ini_set('max_input_time', 450);
        ini_set('max_execution_time', 450);

        if($validator->passes()) {
            $size = $request->file('file')->getSize()/1024;
            if ($size > 6000){
                \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. Tente excluir as unidades com Status = Fechado Definitivamente, e Usados pela Contabilidade.'
                        , 'class' => 'red white-text']);
                                return redirect()->route('importacao');
            }else{

                $unidades = Excel::toArray(new ImportUnidades,  request()->file('file'));
            }


            foreach($unidades as $dados) {
                foreach($dados as $registro) {

                    $res = DB::table('unidades')
                        ->where('an8', '=',  (int)$registro['no_cad_geral'])
                        ->select(
                            'unidades.*'
                        )
                    ->first();

                    $tipodeunidade = DB::table('tiposdeunidade')
                        ->where('codigo', '=',  (int)$registro['tipo_do_orgao'])
                        ->orWhere('tipodescricao', '=',  $registro['descricao_tp_orgao'])
                        ->select(
                            'tiposdeunidade.id'
                        )
                        ->first();
                    //***  gravar somente se tiver tipo de unidade prevista para inspeção
                    if(! empty( $tipodeunidade )) {
                        $enderecounidades = DB::table('unidade_enderecos')
                            ->where('mcu', '=',  (int)$registro['unidades_de_negocios'])
                            ->select(
                                'unidade_enderecos.id'
                            )
                            ->first();
                        if(! empty(  $enderecounidades)) {
                            $enderecos = UnidadeEndereco::find($enderecounidades->id);
                            $enderecos->codIbge =	$registro['codigo_ibge_do_municipio'];
                            $enderecos->endereco = $registro['endereco'];
                            $enderecos->complemento =	$registro['complemento_endereco'];
                            $enderecos->bairro =	$registro['bairro'];
                            $enderecos->cidade =	$registro['cidade'];
                            $enderecos->uf =	$registro['uf'];
                            $enderecos->cep =	$registro['cep'];
                        }
                        else  {
                            $enderecos = new UnidadeEndereco();
                            $enderecos->mcu =	$registro['unidades_de_negocios'];
                            $enderecos->codIbge =	$registro['codigo_ibge_do_municipio'];
                            $enderecos->endereco = $registro['endereco'];
                            $enderecos->complemento =	$registro['complemento_endereco'];
                            $enderecos->bairro =	$registro['bairro'];
                            $enderecos->cidade =	$registro['cidade'];
                            $enderecos->uf =	$registro['uf'];
                            $enderecos->cep =	$registro['cep'];
                        }
                        if (! $res) {
                            $unidade = new Unidade;
                            $unidade->tipoUnidade_id      = $tipodeunidade->id;
                            $unidade->mcu =$registro['unidades_de_negocios'];
                            $unidade->se =$registro['dr'];
                            $unidade->seDescricao =$registro['descricao_dr'];
                            $unidade->an8 =$registro['no_cad_geral'];
                            $unidade->sto =$registro['sto'];
                            $unidade->status_unidade =$registro['status_do_orgao'];
                            $unidade->status_unidadeDesc =$registro['descricao_status'];
                            $unidade->descricao =$registro['nome_fantasia'];
                            $unidade->tipoOrgaoCod =$registro['tipo_do_orgao'];
                            $unidade->tipoOrgaoDesc =$registro['descricao_tp_orgao'];
                            $unidade->cnpj =$registro['cnpj'];
                            $unidade->categoria =$registro['categoria'];
                            $unidade->mecanizacao =$registro['descricao_do_tp_mecanizacao'];
                            $unidade->faixaCepIni =$registro['faixa_ini_cep'];
                            $unidade->faixaCepFim =$registro['faixa_fim_cep'];
                            $unidade->tem_distribuicao =$registro['distribuicao'];
                            $unidade->quantidade_guiches =$registro['quantidade_guiches'];
                            $unidade->guiches_ocupados =$registro['guiches_ocupados'];
                            $unidade->ddd =$registro['ddd'];
                            $unidade->telefone =$registro['telefone_principal'];
                            $unidade->mcu_subordinacaoAdm =$registro['subordinacao_administrativa'];
                            $unidade->desc_subordinacaoAdm =$registro['descricao_subordinacao_adm'];
                            $unidade->nomeResponsavelUnidade =$registro['nome_responsavel'];
                            $unidade->documentRespUnidade =$registro['matricula_responsavel'];
                            $unidade->email=$registro['email_da_unidade'];
                            $unidade->tipoEstrutura = $registro['tipo_de_estrutura'];
                            $unidade->subordinacao_tecnica =$registro['subordinacao_tecnica'];

                            if(!empty($registro['inicio_expediente'])) {
                                $unidade->inicio_expediente =$registro['inicio_expediente'];
                                $unidade->final_expediente =$registro['final_expediente'];
                                $unidade->inicio_intervalo_refeicao =$registro['inicio_intervalo_refeicao'];
                                $unidade->final_intervalo_refeicao =$registro['final_intervalo_refeicao'];
                                $unidade->trabalha_sabado =$registro['trabalha_sabado'];
                                $unidade->inicio_expediente_sabado =$registro['inicio_expediente_sabado'];
                                $unidade->final_expediente_sabado =$registro['final_expediente_sabado'];
                                $unidade->trabalha_domingo =$registro['trabalha_domingo'];
                                $unidade->inicio_expediente_domingo =$registro['inicio_expediente_domingo'];
                                $unidade->final_expediente_domingo =$registro['final_expediente_domingo'];
                                $unidade->tem_plantao =$registro['tem_plantao'];
                                $unidade->inicio_plantao_sabado =$registro['inicio_plantao_sabado'];
                                $unidade->final_plantao_sabado =$registro['final_plantao_sabado'];
                                $unidade->inicio_plantao_domingo =$registro['inicio_plantao_domingo'];
                                $unidade->final_plantao_domingo =$registro['final_plantao_domingo'];
                                $unidade->inicio_distribuicao =$registro['inicio_distribuicao'];
                                $unidade->final_distribuicao =$registro['final_distribuicao'];
                                $unidade->horario_lim_post_na_semana =$registro['horario_lim_post_na_semana'];
                                $unidade->horario_lim_post_final_semana =$registro['horario_lim_post_final_semana'];
                            }
                            $unidade->save();
                            $enderecos->save();
                        }
                        else {
                            $unidade = Unidade::find($res->id);
                            $unidade->tipoUnidade_id      = $tipodeunidade->id;
                            $unidade->mcu =$registro['unidades_de_negocios'];
                            $unidade->se =$registro['dr'];
                            $unidade->seDescricao =$registro['descricao_dr'];
                            $unidade->sto =$registro['sto'];
                            $unidade->status_unidade =$registro['status_do_orgao'];
                            $unidade->status_unidadeDesc =$registro['descricao_status'];
                            $unidade->descricao =$registro['nome_fantasia'];
                            $unidade->tipoOrgaoCod =$registro['tipo_do_orgao'];
                            $unidade->tipoOrgaoDesc =$registro['descricao_tp_orgao'];
                            $unidade->cnpj =$registro['cnpj'];
                            $unidade->categoria =$registro['categoria'];
                            $unidade->mecanizacao =$registro['descricao_do_tp_mecanizacao'];
                            $unidade->faixaCepIni =$registro['faixa_ini_cep'];
                            $unidade->faixaCepFim =$registro['faixa_fim_cep'];
                            $unidade->tem_distribuicao =$registro['distribuicao'];
                            $unidade->quantidade_guiches =$registro['quantidade_guiches'];
                            $unidade->guiches_ocupados =$registro['guiches_ocupados'];
                            $unidade->ddd =$registro['ddd'];
                            $unidade->telefone =$registro['telefone_principal'];
                            $unidade->mcu_subordinacaoAdm =$registro['subordinacao_administrativa'];
                            $unidade->desc_subordinacaoAdm =$registro['descricao_subordinacao_adm'];
                            $unidade->nomeResponsavelUnidade =$registro['nome_responsavel'];
                            $unidade->documentRespUnidade =$registro['matricula_responsavel'];
                            $unidade->email=$registro['email_da_unidade'];
                            $unidade->tipoEstrutura = $registro['tipo_de_estrutura'];
                            $unidade->subordinacao_tecnica =$registro['subordinacao_tecnica'];
//   10/02/2021 - Abilio - Não atualizar dados de horário
//                            if(!empty($registro['inicio_expediente']))
//                            {
//                                $unidade->inicio_atendimento =$registro['inicio_atendimento'];
//                                $unidade->final_atendimento =$registro['final_atendimento'];
//                                $unidade->inicio_expediente =$registro['inicio_expediente'];
//                                $unidade->final_expediente =$registro['final_expediente'];
//                                $unidade->inicio_intervalo_refeicao =$registro['inicio_intervalo_refeicao'];
//                                $unidade->final_intervalo_refeicao =$registro['final_intervalo_refeicao'];
//                                $unidade->trabalha_sabado =$registro['trabalha_sabado'];
//                                $unidade->inicio_expediente_sabado =$registro['inicio_expediente_sabado'];
//                                $unidade->final_expediente_sabado =$registro['final_expediente_sabado'];
//                                $unidade->trabalha_domingo =$registro['trabalha_domingo'];
//                                $unidade->inicio_expediente_domingo =$registro['inicio_expediente_domingo'];
//                                $unidade->final_expediente_domingo =$registro['final_expediente_domingo'];
//                                $unidade->tem_plantao =$registro['tem_plantao'];
//                                $unidade->inicio_plantao_sabado =$registro['inicio_plantao_sabado'];
//                                $unidade->final_plantao_sabado =$registro['final_plantao_sabado'];
//                                $unidade->inicio_plantao_domingo =$registro['inicio_plantao_domingo'];
//                                $unidade->final_plantao_domingo =$registro['final_plantao_domingo'];
//                                $unidade->inicio_distribuicao =$registro['inicio_distribuicao'];
//                                $unidade->final_distribuicao =$registro['final_distribuicao'];
//                                $unidade->horario_lim_post_na_semana =$registro['horario_lim_post_na_semana'];
//                                $unidade->horario_lim_post_final_semana =$registro['horario_lim_post_final_semana'];
//                            }
                            $unidade->update();
                            $enderecos->update();
                        }
                    }
                }
            }



            \Session::flash('mensagem',['msg'=>'O Arquivo de Unidades foi importado.'
                 ,'class'=>'green white-text']);

            return redirect()->route('importacao');


        }else{
            return back()->with(['errors'=>$validator->errors()->all()]);
        }
    }
    public function unidades() {
        return view('compliance.importacoes.unidades');  //
    }

    public function importAdicionalUnidades(Request $request) {
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xlsx'
        ]);
        ini_set('memory_limit', '512M');
        ini_set('max_input_time', 450);
        ini_set('max_execution_time', 450);

        if($validator->passes()) {
            $size = $request->file('file')->getSize()/1024;
            if ($size > 6000){
                \Session::flash('mensagem', ['msg' => 'O arquivo é muito grande. Tente excluir as unidades com Status = Fechado Definitivamente, e Usados pela Contabilidade.'
                    , 'class' => 'red white-text']);
                return redirect()->route('importacao');
            }else{

                $unidades = Excel::toArray(new ImportUnidades,  request()->file('file'));
            }

            foreach($unidades as $dados) {
                foreach($dados as $registro) {

                    $inicio_expediente = null ; //      "h_expediente" => " - "
                    $final_expediente = null ; //      "h_expediente" => " - "
                    $inicio_atendimento = null ; //      "h_expediente" => " - "
                    $final_atendimento = null ;//      "h_expediente" => " - "
                    $inicio_intervalo_refeicao = null ; //      "h_almoco" => " - "
                    $final_intervalo_refeicao= null ; //      "h_almoco" => " - "
                    $inicio_expediente_sabado = null ; //      "h_sabado" => " - "
                    $final_expediente_sabado = null ; //      "h_sabado" => " - "
                    $inicio_distribuicao = null ; //      "h_distrib" => " - "
                    $final_distribuicao = null ; //      "h_distrib" => " - "
                    $inicio_expediente_domingo = null ; //      "h_domingo" => " - "
                    $final_expediente_domingo = null ; //      "h_domingo" => " - "
                    $inicio_plantao_sabado = null ; //      "h_plantao_sab" => " - "
                    $final_plantao_sabado = null ; //      "h_plantao_sab" => " - "
                    $inicio_plantao_domingo = null ; //      "h_plantao_dom" => " - "
                    $final_plantao_domingo = null ; //      "h_plantao_dom" => " - "

//  possiveis tamanhos   3 5 8 13

//         ABILIO           14/04/2021 - AGUARDAR SE A LUCIANA CONSEGUE O RELATÓRIO ERP R5501A COM OS CAMPOS POPULADOS
                    dd('ABILIO           14/04/2021 - AGUARDAR SE A LUCIANA CONSEGUE O RELATÓRIO ERP R5501A COM OS CAMPOS POPULADOS É POSSIVEL NÃO PRECISAR DESSA ROTINA');
                    if(!empty($registro['h_expediente'])){
                        $size = strlen ($registro['h_expediente']);

                        dd(substr($registro['h_expediente'], 0, 5));
                        $conteudo = substr($registro['h_expediente'], 0, 4);


                        if ($size == 5){

                            $inicio_expediente = $registro['h_expediente'];
                            $final_expediente = $registro['h_expediente'];
                            $inicio_atendimento = $conteudo;
                            $final_atendimento = $registro['h_expediente'];
                        }

                    }
                    if(!empty($registro['h_almoco'])){
//                        $inicio_intervalo_refeicao
//                        $final_intervalo_refeicao
                    }


                    if($registro['trab_sab_sn'] == 'Sim' ){

                        $inicio_expediente_sabado = null ; //      "h_sabado" => " - "
                        $final_expediente_sabado = null ; //      "h_sabado" => " - "
                    }
                    if($registro['distrib_sn'] == 'Sim' ){

                        $inicio_distribuicao = null ; //      "h_distrib" => " - "
                        $final_distribuicao = null ; //      "h_distrib" => " - "
                    }

                    if($registro['trabalha_domingo'] == 'Sim' ){

                        $inicio_expediente_domingo = null ; //      "h_domingo" => " - "
                        $final_expediente_domingo = null ; //      "h_domingo" => " - "
                    }
                    if($registro['tem_plantao'] == 'Sim' ){

                        $inicio_plantao_sabado = null ; //      "h_plantao_sab" => " - "
                        $final_plantao_sabado = null ; //      "h_plantao_sab" => " - "
                        $inicio_plantao_domingo = null ; //      "h_plantao_dom" => " - "
                        $final_plantao_domingo = null ; //      "h_plantao_dom" => " - "
                    }

                    dd($registro['orgao'],    '    <--  line 2175  ->> ', (int)$registro['orgao']);

                    Unidade  :: updateOrFirst([
                        'mcu' => (int)$registro['orgao'],
                    ],[
                        'mcu' => (int)$registro['orgao'],
                        'trabalha_sabado' => $registro['trab_sab_sn'],
                        'trabalha_domingo' => $registro['trab_dom_sn'],
                        'tem_plantao' => $registro['plantao_sn'],
                        'tem_distribuicao' => $registro['distrib_sn'],
                        'horario_lim_post_na_semana' => $registro['dh'],
                        'horario_lim_post_final_semana' => $registro['dh_fds'],

                    ]);


                    $res = DB::table('unidades')
                        ->where('an8', '=',  (int)$registro['no_cad_geral'])
                        ->select(
                            'unidades.*'
                        )
                        ->first();

                    $tipodeunidade = DB::table('tiposdeunidade')
                        ->where('codigo', '=',  (int)$registro['tipo_do_orgao'])
                        ->orWhere('tipodescricao', '=',  $registro['descricao_tp_orgao'])
                        ->select(
                            'tiposdeunidade.id'
                        )
                        ->first();
                    //***  gravar somente se tiver tipo de unidade prevista para inspeção
                    if(! empty( $tipodeunidade )) {
                        $enderecounidades = DB::table('unidade_enderecos')
                            ->where('mcu', '=',  (int)$registro['unidades_de_negocios'])
                            ->select(
                                'unidade_enderecos.id'
                            )
                            ->first();
                        if(! empty(  $enderecounidades)) {
                            $enderecos = UnidadeEndereco::find($enderecounidades->id);
                            $enderecos->codIbge =	$registro['codigo_ibge_do_municipio'];
                            $enderecos->endereco = $registro['endereco'];
                            $enderecos->complemento =	$registro['complemento_endereco'];
                            $enderecos->bairro =	$registro['bairro'];
                            $enderecos->cidade =	$registro['cidade'];
                            $enderecos->uf =	$registro['uf'];
                            $enderecos->cep =	$registro['cep'];
                        }
                        else  {
                            $enderecos = new UnidadeEndereco();
                            $enderecos->mcu =	$registro['unidades_de_negocios'];
                            $enderecos->codIbge =	$registro['codigo_ibge_do_municipio'];
                            $enderecos->endereco = $registro['endereco'];
                            $enderecos->complemento =	$registro['complemento_endereco'];
                            $enderecos->bairro =	$registro['bairro'];
                            $enderecos->cidade =	$registro['cidade'];
                            $enderecos->uf =	$registro['uf'];
                            $enderecos->cep =	$registro['cep'];
                        }
                        if (! $res) {
                            $unidade = new Unidade;
                            $unidade->tipoUnidade_id      = $tipodeunidade->id;
                            $unidade->mcu =$registro['unidades_de_negocios'];
                            $unidade->se =$registro['dr'];
                            $unidade->seDescricao =$registro['descricao_dr'];
                            $unidade->an8 =$registro['no_cad_geral'];
                            $unidade->sto =$registro['sto'];
                            $unidade->status_unidade =$registro['status_do_orgao'];
                            $unidade->status_unidadeDesc =$registro['descricao_status'];
                            $unidade->descricao =$registro['nome_fantasia'];
                            $unidade->tipoOrgaoCod =$registro['tipo_do_orgao'];
                            $unidade->tipoOrgaoDesc =$registro['descricao_tp_orgao'];
                            $unidade->cnpj =$registro['cnpj'];
                            $unidade->categoria =$registro['categoria'];
                            $unidade->mecanizacao =$registro['descricao_do_tp_mecanizacao'];
                            $unidade->faixaCepIni =$registro['faixa_ini_cep'];
                            $unidade->faixaCepFim =$registro['faixa_fim_cep'];
                            $unidade->tem_distribuicao =$registro['distribuicao'];
                            $unidade->quantidade_guiches =$registro['quantidade_guiches'];
                            $unidade->guiches_ocupados =$registro['guiches_ocupados'];
                            $unidade->ddd =$registro['ddd'];
                            $unidade->telefone =$registro['telefone_principal'];
                            $unidade->mcu_subordinacaoAdm =$registro['subordinacao_administrativa'];
                            $unidade->desc_subordinacaoAdm =$registro['descricao_subordinacao_adm'];
                            $unidade->nomeResponsavelUnidade =$registro['nome_responsavel'];
                            $unidade->documentRespUnidade =$registro['matricula_responsavel'];
                            $unidade->email=$registro['email_da_unidade'];
                            $unidade->tipoEstrutura = $registro['tipo_de_estrutura'];
                            $unidade->subordinacao_tecnica =$registro['subordinacao_tecnica'];

                            if(!empty($registro['inicio_expediente'])) {
                                $unidade->inicio_expediente =$registro['inicio_expediente'];
                                $unidade->final_expediente =$registro['final_expediente'];
                                $unidade->inicio_intervalo_refeicao =$registro['inicio_intervalo_refeicao'];
                                $unidade->final_intervalo_refeicao =$registro['final_intervalo_refeicao'];
                                $unidade->trabalha_sabado =$registro['trabalha_sabado'];
                                $unidade->inicio_expediente_sabado =$registro['inicio_expediente_sabado'];
                                $unidade->final_expediente_sabado =$registro['final_expediente_sabado'];
                                $unidade->trabalha_domingo =$registro['trabalha_domingo'];
                                $unidade->inicio_expediente_domingo =$registro['inicio_expediente_domingo'];
                                $unidade->final_expediente_domingo =$registro['final_expediente_domingo'];
                                $unidade->tem_plantao =$registro['tem_plantao'];
                                $unidade->inicio_plantao_sabado =$registro['inicio_plantao_sabado'];
                                $unidade->final_plantao_sabado =$registro['final_plantao_sabado'];
                                $unidade->inicio_plantao_domingo =$registro['inicio_plantao_domingo'];
                                $unidade->final_plantao_domingo =$registro['final_plantao_domingo'];
                                $unidade->inicio_distribuicao =$registro['inicio_distribuicao'];
                                $unidade->final_distribuicao =$registro['final_distribuicao'];
                                $unidade->horario_lim_post_na_semana =$registro['horario_lim_post_na_semana'];
                                $unidade->horario_lim_post_final_semana =$registro['horario_lim_post_final_semana'];
                            }
                            $unidade->save();
                            $enderecos->save();
                        }
                        else {
                            $unidade = Unidade::find($res->id);
                            $unidade->tipoUnidade_id      = $tipodeunidade->id;
                            $unidade->mcu =$registro['unidades_de_negocios'];
                            $unidade->se =$registro['dr'];
                            $unidade->seDescricao =$registro['descricao_dr'];
                            $unidade->sto =$registro['sto'];
                            $unidade->status_unidade =$registro['status_do_orgao'];
                            $unidade->status_unidadeDesc =$registro['descricao_status'];
                            $unidade->descricao =$registro['nome_fantasia'];
                            $unidade->tipoOrgaoCod =$registro['tipo_do_orgao'];
                            $unidade->tipoOrgaoDesc =$registro['descricao_tp_orgao'];
                            $unidade->cnpj =$registro['cnpj'];
                            $unidade->categoria =$registro['categoria'];
                            $unidade->mecanizacao =$registro['descricao_do_tp_mecanizacao'];
                            $unidade->faixaCepIni =$registro['faixa_ini_cep'];
                            $unidade->faixaCepFim =$registro['faixa_fim_cep'];
                            $unidade->tem_distribuicao =$registro['distribuicao'];
                            $unidade->quantidade_guiches =$registro['quantidade_guiches'];
                            $unidade->guiches_ocupados =$registro['guiches_ocupados'];
                            $unidade->ddd =$registro['ddd'];
                            $unidade->telefone =$registro['telefone_principal'];
                            $unidade->mcu_subordinacaoAdm =$registro['subordinacao_administrativa'];
                            $unidade->desc_subordinacaoAdm =$registro['descricao_subordinacao_adm'];
                            $unidade->nomeResponsavelUnidade =$registro['nome_responsavel'];
                            $unidade->documentRespUnidade =$registro['matricula_responsavel'];
                            $unidade->email=$registro['email_da_unidade'];
                            $unidade->tipoEstrutura = $registro['tipo_de_estrutura'];
                            $unidade->subordinacao_tecnica =$registro['subordinacao_tecnica'];
//   10/02/2021 - Abilio - Não atualizar dados de horário
//                            if(!empty($registro['inicio_expediente']))
//                            {
//                                $unidade->inicio_atendimento =$registro['inicio_atendimento'];
//                                $unidade->final_atendimento =$registro['final_atendimento'];
//                                $unidade->inicio_expediente =$registro['inicio_expediente'];
//                                $unidade->final_expediente =$registro['final_expediente'];
//                                $unidade->inicio_intervalo_refeicao =$registro['inicio_intervalo_refeicao'];
//                                $unidade->final_intervalo_refeicao =$registro['final_intervalo_refeicao'];
//                                $unidade->trabalha_sabado =$registro['trabalha_sabado'];
//                                $unidade->inicio_expediente_sabado =$registro['inicio_expediente_sabado'];
//                                $unidade->final_expediente_sabado =$registro['final_expediente_sabado'];
//                                $unidade->trabalha_domingo =$registro['trabalha_domingo'];
//                                $unidade->inicio_expediente_domingo =$registro['inicio_expediente_domingo'];
//                                $unidade->final_expediente_domingo =$registro['final_expediente_domingo'];
//                                $unidade->tem_plantao =$registro['tem_plantao'];
//                                $unidade->inicio_plantao_sabado =$registro['inicio_plantao_sabado'];
//                                $unidade->final_plantao_sabado =$registro['final_plantao_sabado'];
//                                $unidade->inicio_plantao_domingo =$registro['inicio_plantao_domingo'];
//                                $unidade->final_plantao_domingo =$registro['final_plantao_domingo'];
//                                $unidade->inicio_distribuicao =$registro['inicio_distribuicao'];
//                                $unidade->final_distribuicao =$registro['final_distribuicao'];
//                                $unidade->horario_lim_post_na_semana =$registro['horario_lim_post_na_semana'];
//                                $unidade->horario_lim_post_final_semana =$registro['horario_lim_post_final_semana'];
//                            }
                            $unidade->update();
                            $enderecos->update();
                        }
                    }
                }
            }



            \Session::flash('mensagem',['msg'=>'O Arquivo de Unidades foi importado.'
                ,'class'=>'green white-text']);

            return redirect()->route('importacao');


        }else{
            return back()->with(['errors'=>$validator->errors()->all()]);
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
