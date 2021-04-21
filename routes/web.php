<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('principal/welcome');
});

Route::get('/sobre', 'Site\PaginaController@sobre')->name('site.sobre');
Route::get('/contato', 'Site\PaginaController@contato')->name('site.contato');
Route::put('/contato/enviar', 'Site\PaginaController@enviarContato')->name('site.contato.enviar');

Auth::routes();

Route::get('/principal/home', 'HomeController@index')->name('home');
Route::get('/sair', 'Admin\UsuarioController@sair')->name('sair');

Route::get('/admin/slides', 'Admin\SlideController@index')->name('admin.slides');
Route::get('/admin/slides/adicionar', 'Admin\SlideController@adicionar')->name('admin.slides.adicionar');
Route::post('/admin/slides/salvar', 'Admin\SlideController@salvar')->name('admin.slides.salvar');
Route::get('/admin/slides/editar/{id}', 'Admin\SlideController@editar')->name('admin.slides.editar');
Route::get('/admin/slides/deletar/{id}', 'Admin\SlideController@deletar')->name('admin.slides.deletar');
Route::put('/admin/slides/atualizar/{id}', 'Admin\SlideController@atualizar')->name('admin.slides.atualizar');

//Administando o Autorizações para Usuários do site
Route::get('/admin/papel', 'Admin\PapelController@index')->name('admin.papel');
Route::get('/admin/papel/adicionar', 'Admin\PapelController@adicionar')->name('admin.papel.adicionar');
Route::post('/admin/papel/salvar', 'Admin\PapelController@salvar')->name('admin.papel.salvar');
Route::get('/admin/papel/editar/{id}', 'Admin\PapelController@editar')->name('admin.papel.editar');
Route::put('/admin/papel/atualizar/{id}', 'Admin\PapelController@atualizar')->name('admin.papel.atualizar');
Route::get('/admin/papel/deletar/{id}', 'Admin\PapelController@deletar')->name('admin.papel.deletar');

//Administando o Permissões para Usuários do site
Route::get('/admin/papel/permissao/{id}', 'Admin\PapelController@permissao')->name('admin.papel.permissao');
Route::post('/admin/papel/permissao/{id}/salvar', 'Admin\PapelController@salvarPermissao')->name('admin.papel.permissao.salvar');
Route::get('/admin/papel/permissao/{id}', 'Admin\PapelController@permissao')->name('admin.papel.permissao');
Route::get('/admin/papel/permissao/{id}/remover/{id_permissao}','Admin\PapelController@removerPermissao')->name('admin.papel.permissao.remover');

//Administando o Papeis Usuários do site
Route::get('/admin/usuarios/papel/{id}', 'Admin\UsuarioController@papel')->name('admin.usuarios.papel');
Route::post('/admin/usuarios/papel/salvar/{id}', 'Admin\UsuarioController@salvarPapel')->name('admin.usuarios.papel.salvar');
Route::get('/admin/usuarios/papel/remover/{id}/{papel_id}', 'Admin\UsuarioController@removerPapel')->name('admin.usuarios.papel.remover');

//Administando o Usuários do site
Route::get('/admin/usuarios', 'Admin\UsuarioController@index')->name('admin.usuarios');
Route::get('/admin/usuarios/adicionar', 'Admin\UsuarioController@adicionar')->name('admin.usuarios.adicionar');
Route::post('/admin/usuarios/salvar', 'Admin\UsuarioController@salvar')->name('admin.usuarios.salvar');
Route::get('/admin/usuarios/editar/{id}', 'Admin\UsuarioController@editar')->name('admin.usuarios.editar');
Route::put('/admin/usuarios/atualizar/{id}', 'Admin\UsuarioController@atualizar')->name('admin.usuarios.atualizar');
Route::get('/admin/usuarios/deletar/{id}', 'Admin\UsuarioController@deletar')->name('admin.usuarios.deletar');
Route::post('/admin/usuarios/{search?}', 'Admin\UsuarioController@search')->name('admin.usuarios.search');

//Administando o paginas do site
Route::get('/admin/paginas', 'Admin\PaginasController@index')->name('admin.paginas');
Route::get('/admin/paginas/editar/{id}', 'Admin\PaginasController@editar')->name('admin.paginas.editar');
Route::put('/admin/paginas/atualizar/{id}', 'Admin\PaginasController@atualizar')->name('admin.paginas.atualizar');

Route::post('/compliance/unidades/salvarInspecao', 'Correios\UnidadesController@salvarInspecao')->name('compliance.unidades.salvarInspecao');
Route::get('/compliance/unidades/gerarInspecao/{id}', 'Correios\UnidadesController@gerarInspecao')->name('compliance.unidades.gerarInspecao');
Route::put('/compliance/unidades/atualizar/{id}','Correios\UnidadesController@atualizar')->name('compliance.unidades.atualizar');
Route::get('/compliance/unidades/editar/{id}', 'Correios\UnidadesController@edit')->name('compliance.unidades.editar');
Route::get('/compliance/unidades', 'Correios\UnidadesController@index')->name('compliance.unidades');
Route::post('/compliance/unidades{id?}', 'Correios\UnidadesController@search')->name('compliance.unidades.search');

Route::get('/compliance/grupoVerificacao/destroy/{id}', 'Correios\GruposDeVerificacaoController@destroy')->name('compliance.grupoVerificacao.destroy');
Route::post('/compliance/grupoVerificacao/salvar', 'Correios\GruposDeVerificacaoController@salvar')->name('compliance.grupoVerificacao.salvar');
Route::get('/compliance/grupoVerificacao/adicionar', 'Correios\GruposDeVerificacaoController@adicionar')->name('compliance.grupoVerificacao.adicionar');
Route::put('/compliance/grupoVerificacao/atualizar/{id}','Correios\GruposDeVerificacaoController@atualizar')->name('compliance.grupoVerificacao.atualizar');
Route::get('/compliance/grupoVerificacao/editar/{id}', 'Correios\GruposDeVerificacaoController@edit')->name('compliance.grupoVerificacao.editar');
Route::get('/compliance/grupoVerificacao/', 'Correios\GruposDeVerificacaoController@index')->name('compliance.grupoVerificacao');
Route::post('/compliance/grupoVerificacao/{search?}', 'Correios\GruposDeVerificacaoController@search')->name('compliance.grupoVerificacao.search');

Route::get('/compliance/relatos/destroy/{id}', 'Correios\RelatoController@destroy')->name('compliance.relatos.destroy');
Route::post('/compliance/relatos/salvar', 'Correios\RelatoController@salvar')->name('compliance.relatos.salvar');
Route::get('/compliance/relatos/adicionar', 'Correios\RelatoController@adicionar')->name('compliance.relatos.adicionar');
Route::put('/compliance/relatos/atualizar/{id}','Correios\RelatoController@atualizar')->name('compliance.relatos.atualizar');
Route::get('/compliance/relatos/editar/{id}', 'Correios\RelatoController@edit')->name('compliance.relatos.editar');
Route::get('/compliance/relatos/', 'Correios\RelatoController@index')->name('compliance.relatos');
Route::post('/compliance/relatos/{search?}', 'Correios\RelatoController@search')->name('compliance.relatos.search');

Route::get('/compliance/inspecionados', 'Correios\InspecionadosController@index')->name('compliance.inspecionados');
Route::post('/compliance/inspecionados{search?}', 'Correios\InspecionadosController@search')->name('compliance.inspecionados.search');
Route::get('/compliance/inspecionados/{id}', 'Correios\InspecionadosController@papelTrabalho')->name('compliance.inspecionados.papelTrabalho');
Route::get('/compliance/inspecionados/pdf/{id}','Correios\InspecionadosController@createPDF')->name('compliance.inspecionados.pdfPapelTrabalho');
Route::get('/compliance/inspecionados/xml/{id}','Correios\InspecionadosController@createXML')->name('compliance.inspecionados.xml');
Route::get('/compliance/inspecionados/recusar/{id}', 'Correios\InspecionadosController@recusar')->name('compliance.inspecionados.recusar');

Route::get('/compliance/verificacoes/destroy/{id}', 'Correios\VerificacoesController@destroy')->name('compliance.verificacoes.destroy');
Route::get('/compliance/verificacoes/', 'Correios\VerificacoesController@index')->name('compliance.verificacoes');
Route::post('/compliance/verificacoes/{search?}', 'Correios\VerificacoesController@search')->name('compliance.verificacoes.search');

Route::put('/compliance/tipounidades/atualizar/{id}','Correios\TipoDeUnidadeController@update')->name('compliance.tipounidades.atualizar');
Route::get('/compliance/tipounidades', 'Correios\TipoDeUnidadeController@index')->name('compliance.tipounidades');
Route::get('/compliance/tipounidades/editar/{id}', 'Correios\TipoDeUnidadeController@edit')->name('compliance.tipounidades.editar');
Route::get('/compliance/tipounidades/adicionar', 'Correios\TipoDeUnidadeController@adicionar')->name('compliance.tipounidades.adicionar');
Route::post('/compliance/tipounidades/salvar', 'Correios\TipoDeUnidadeController@salvar')->name('compliance.tipounidades.salvar');
Route::post('/compliance/tipounidades/{search?}', 'Correios\TipoDeUnidadeController@search')->name('compliance.tipounidades.search');

Route::get('/compliance/inspecao/destroyfiles/{id}', 'Correios\InspecaoController@deletefiles')->name('compliance.inspecao.destroyfiles');
Route::put('/compliance/inspecao/atualizar/{id}','Correios\InspecaoController@update')->name('compliance.inspecao.atualizar');
//    atualizarsro
Route::get('/compliance/inspecao/editar/{id}', 'Correios\InspecaoController@edit')->name('compliance.inspecao.editar');
Route::put('/compliance/inspecao/editsro/{id}', 'Correios\InspecaoController@editsro')->name('compliance.inspecao.editar.sro');
Route::get('/compliance/inspecao/exportsro/{id}', 'Correios\InspecaoController@exportsro')->name('compliance.inspecao.exportsro');

//**
// Rota nomeada com mais de um parâmetro
//Route::get('/compliance/inspecao/lancamentossro/export/{idCodigo}/numeroGrupoVerificacao/{idNumeroGrupoVerificacao}/numeroDoTeste/{idNumeroDoTeste}', 'Correios\InspecaoController@exportLancamentosSRO')->name('compliance.inspecao.export');

//  Route::get('/compliance/inspecao/lancamentossro/export/{codigo}', 'Correios\InspecaoController@exportLancamentosSRO')->name('compliance.inspecao.export.sro');

Route::get('/compliance/inspecao/{id}', 'Correios\InspecaoController@index')->name('compliance.inspecao');
Route::get('/compliance/inspecao/corroborar/{id}', 'Correios\InspecaoController@corroborar')->name('compliance.inspecao.corroborar');
Route::post('/compliance/inspecao/{search?}', 'Correios\InspecaoController@search')->name('compliance.inspecao.search');
Route::get('/compliance/importacoes', 'Correios\Importacao\ImportacaoController@index')->name('importacao');

/**
 * inicio Rotas para importações
 */

Route::get('/compliance/importacoes/alarme/export', 'Correios\Importacao\ImportacaoController@exportAlarme')->name('compliance.export.alarme');
Route::post('/compliance/importacoes/alarme', 'Correios\Importacao\ImportacaoController@importAlarme')->name('compliance.importacao.alarme');
Route::get('/compliance/importacoes/alarme', 'Correios\Importacao\ImportacaoController@alarme')->name('importacao.alarme');

Route::get('/compliance/importacoes/debitoEmpregado/export', 'Correios\Importacao\ImportacaoController@exportDebitoEmpregados')->name('compliance.export.webcont');
Route::post('/compliance/importacoes/debitoEmpregado', 'Correios\Importacao\ImportacaoController@importDebitoEmpregados')->name('compliance.importacao.webcont');
Route::get('/compliance/importacoes/debitoEmpregado', 'Correios\Importacao\ImportacaoController@debitoEmpregados')->name('importacao.webcont');

Route::get('/compliance/importacoes/proter/export', 'Correios\Importacao\ImportacaoController@exportProter')->name('compliance.export.proter');
Route::post('/compliance/importacoes/proter', 'Correios\Importacao\ImportacaoController@importProter')->name('compliance.importacao.proter');
Route::get('/compliance/importacoes/proter', 'Correios\Importacao\ImportacaoController@proter')->name('importacao.proter');

Route::get('/compliance/importacoes/cadastral/export', 'Correios\Importacao\ImportacaoController@exportCadastral')->name('compliance.export.cadastral');
Route::post('/compliance/importacoes/cadastral', 'Correios\Importacao\ImportacaoController@importCadastral')->name('compliance.importacao.cadastral');
Route::get('/compliance/importacoes/cadastral', 'Correios\Importacao\ImportacaoController@cadastral')->name('importacao.cadastral');

Route::post('/compliance/importacoes/unidades', 'Correios\Importacao\ImportacaoController@importUnidades')->name('compliance.importacao.unidades');
Route::get('/compliance/importacoes/unidades', 'Correios\Importacao\ImportacaoController@unidades')->name('importacao.unidades');

Route::post('/compliance/importacoes/unidadesAdicional', 'Correios\Importacao\ImportacaoController@importAdicionalUnidades')->name('compliance.importacao.adicionalunidades');
Route::get('/compliance/importacoes/unidadesAdicional', 'Correios\Importacao\ImportacaoController@unidadesAdicional')->name('importacao.adicionalunidades');

Route::get('/compliance/importacoes/smb_bdf/export', 'Correios\Importacao\ImportacaoController@exportSmb_bdf')->name('compliance.export.smb_bdf');
Route::post('/compliance/importacoes/smb_bdf', 'Correios\Importacao\ImportacaoController@importSmb_bdf')->name('compliance.importacao.smb_bdf');
Route::get('/compliance/importacoes/smb_bdf', 'Correios\Importacao\ImportacaoController@smb_bdf')->name('importacao.smb_bdf');

Route::get('/compliance/importacoes/SL02_bdf/export', 'Correios\Importacao\ImportacaoController@exportSL02_bdf')->name('compliance.export.SL02_bdf');
Route::post('/compliance/importacoes/SL02_bdf', 'Correios\Importacao\ImportacaoController@importSL02_bdf')->name('compliance.importacao.SL02_bdf');
Route::get('/compliance/importacoes/SL02_bdf', 'Correios\Importacao\ImportacaoController@SL02_bdf')->name('importacao.SL02_bdf');

Route::get('/compliance/importacoes/RespDefinida/export', 'Correios\Importacao\ImportacaoController@exportRespDefinida')->name('compliance.export.RespDefinida');
Route::post('/compliance/importacoes/RespDefinida', 'Correios\Importacao\ImportacaoController@importRespDefinida')->name('compliance.importacao.RespDefinida');
Route::get('/compliance/importacoes/RespDefinida', 'Correios\Importacao\ImportacaoController@RespDefinida')->name('importacao.RespDefinida');

Route::get('/compliance/importacoes/feriado/export', 'Correios\Importacao\ImportacaoController@exportFeriado')->name('compliance.export.feriado');
Route::post('/compliance/importacoes/feriado', 'Correios\Importacao\ImportacaoController@importFeriado')->name('compliance.importacao.feriado');
Route::get('/compliance/importacoes/feriado', 'Correios\Importacao\ImportacaoController@feriado')->name('importacao.feriado');

Route::get('/compliance/importacoes/ferias/export', 'Correios\Importacao\ImportacaoController@exportFerias')->name('compliance.export.ferias');
Route::post('/compliance/importacoes/ferias', 'Correios\Importacao\ImportacaoController@importFerias')->name('compliance.importacao.ferias');
Route::get('/compliance/importacoes/ferias', 'Correios\Importacao\ImportacaoController@ferias')->name('importacao.ferias');

Route::get('/compliance/importacoes/absenteismo/export', 'Correios\Importacao\ImportacaoController@exportAbsenteismo')->name('compliance.export.absenteismo');
Route::post('/compliance/importacoes/absenteismo', 'Correios\Importacao\ImportacaoController@importAbsenteismo')->name('compliance.importacao.absenteismo');
Route::get('/compliance/importacoes/absenteismo', 'Correios\Importacao\ImportacaoController@absenteismo')->name('importacao.absenteismo');

Route::get('/compliance/importacoes/cftv/export', 'Correios\Importacao\ImportacaoController@exportCftv')->name('compliance.export.cftv');
Route::post('/compliance/importacoes/cftv', 'Correios\Importacao\ImportacaoController@importCftv')->name('compliance.importacao.cftv');
Route::get('/compliance/importacoes/cftv', 'Correios\Importacao\ImportacaoController@cftv')->name('importacao.cftv');

Route::get('/compliance/importacoes/controleDeViagem/export', 'Correios\Importacao\ImportacaoController@exportControleDeViagem')->name('compliance.export.controleDeViagem');
Route::post('/compliance/importacoes/controleDeViagem', 'Correios\Importacao\ImportacaoController@importControleDeViagem')->name('compliance.importacao.controleDeViagem');
Route::get('/compliance/importacoes/controleDeViagem', 'Correios\Importacao\ImportacaoController@controleDeViagem')->name('importacao.controleDeViagem');


Route::get('/compliance/importacoes/plpListaPendente/export', 'Correios\Importacao\ImportacaoController@exportPLPListaPendente')->name('compliance.export.plpListaPendente');
Route::post('/compliance/importacoes/plpListaPendente', 'Correios\Importacao\ImportacaoController@importPLPListaPendente')->name('compliance.importacao.plpListaPendente');
Route::get('/compliance/importacoes/plpListaPendente', 'Correios\Importacao\ImportacaoController@plpListaPendente')->name('importacao.plpListaPendente');

Route::get('/compliance/importacoes/sgdoDistribuicao/export', 'Correios\Importacao\ImportacaoController@exportSgdoDistribuicao')->name('compliance.export.sgdoDistribuicao');
Route::post('/compliance/importacoes/sgdoDistribuicao', 'Correios\Importacao\ImportacaoController@importSgdoDistribuicao')->name('compliance.importacao.sgdoDistribuicao');
Route::get('/compliance/importacoes/sgdoDistribuicao', 'Correios\Importacao\ImportacaoController@sgdoDistribuicao')->name('importacao.sgdoDistribuicao');

Route::get('/compliance/importacoes/cieEletronica/export', 'Correios\Importacao\ImportacaoController@exportCieEletronica')->name('compliance.export.cieEletronica');
Route::post('/compliance/importacoes/cieEletronica', 'Correios\Importacao\ImportacaoController@importCieEletronica')->name('compliance.importacao.cieEletronica');
Route::get('/compliance/importacoes/cieEletronica', 'Correios\Importacao\ImportacaoController@cieEletronica')->name('importacao.cieEletronica');

Route::get('/compliance/importacoes/painelExtravio/export', 'Correios\Importacao\ImportacaoController@exportPainelExtravio')->name('compliance.export.painelExtravio');
Route::post('/compliance/importacoes/painelExtravio', 'Correios\Importacao\ImportacaoController@importPainelExtravio')->name('compliance.importacao.painelExtravio');
Route::get('/compliance/importacoes/painelExtravio', 'Correios\Importacao\ImportacaoController@painelExtravio')->name('importacao.painelExtravio');

Route::get('/compliance/importacoes/pagamentosAdicionais/export', 'Correios\Importacao\ImportacaoController@exportPagamentosAdicionais')->name('compliance.export.pagamentosAdicionais');
Route::post('/compliance/importacoes/pagamentosAdicionais', 'Correios\Importacao\ImportacaoController@importPagamentosAdicionais')->name('compliance.importacao.pagamentosAdicionais');
Route::get('/compliance/importacoes/pagamentosAdicionais', 'Correios\Importacao\ImportacaoController@pagamentosAdicionais')->name('importacao.pagamentosAdicionais');

Route::get('/compliance/importacoes/bdf_fat_02/export', 'Correios\Importacao\ImportacaoController@exportBDF_FAT_02')->name('compliance.export.bdf_fat_02');
Route::post('/compliance/importacoes/bdf_fat_02', 'Correios\Importacao\ImportacaoController@importBDF_FAT_02')->name('compliance.importacao.bdf_fat_02');
Route::get('/compliance/importacoes/bdf_fat_02', 'Correios\Importacao\ImportacaoController@bdf_fat_02')->name('importacao.bdf_fat_02');

Route::get('/compliance/importacoes/microStrategy/export', 'Correios\Importacao\ImportacaoController@exportMicroStrategy')->name('compliance.export.microStrategy');
Route::post('/compliance/importacoes/microStrategy', 'Correios\Importacao\ImportacaoController@importMicroStrategy')->name('compliance.importacao.microStrategy');
Route::get('/compliance/importacoes/microStrategy', 'Correios\Importacao\ImportacaoController@microStrategy')->name('importacao.microStrategy');


Route::get('/compliance/importacoes/apontamentoCV/export', 'Correios\Importacao\ImportacaoController@exportApontamentoCV')->name('compliance.export.apontamentoCV');
Route::post('/compliance/importacoes/apontamentoCV', 'Correios\Importacao\ImportacaoController@importApontamentoCV')->name('compliance.importacao.apontamentoCV');
Route::get('/compliance/importacoes/apontamentoCV', 'Correios\Importacao\ImportacaoController@apontamentoCV')->name('importacao.apontamentoCV');





//importacao.  importSnci exportSnci snci

Route::get('/compliance/importacoes/snci/export', 'Correios\Importacao\ImportacaoController@exportSnci')->name('compliance.export.snci');
Route::post('/compliance/importacoes/snci', 'Correios\Importacao\ImportacaoController@importSnci')->name('compliance.importacao.snci');
Route::get('/compliance/importacoes/snci', 'Correios\Importacao\ImportacaoController@snci')->name('importacao.snci');


/**
 * rotas para consolidar funcionalidades da inspeção automática
 */

Route::post('/compliance/monitoramento/avaliacao', 'Correios\MonitoramentoController@avaliacao')->name('compliance.monitoramento.avaliacao');
Route::get('/compliance/monitoramento/avaliar', 'Correios\MonitoramentoController@avaliar')->name('compliance.monitoramento.avaliar');
Route::post('/compliance/monitoramento/create', 'Correios\MonitoramentoController@create')->name('compliance.monitoramento.create');
Route::get('/compliance/monitoramento/criar', 'Correios\MonitoramentoController@criar')->name('compliance.monitoramento.criar');
Route::get('/compliance/monitoramento/show', 'Correios\MonitoramentoController@show')->name('compliance.monitoramento.show');
Route::get('/compliance/monitoramento/xml', 'Correios\MonitoramentoController@xml')->name('compliance.monitoramento.xml');

Route::post('/compliance/monitoramento/gerar_xml', 'Correios\MonitoramentoController@gerar_xml')->name('compliance.monitoramento.gerar_xml');



//Route::post('/compliance/monitoramento/create', 'Correios\UnidadesController@salvarInspecao')->name('compliance.unidades.salvarInspecao');

//Route::post('/compliance/unidades/salvarInspecao', 'Correios\UnidadesController@salvarInspecao')->name('compliance.unidades.salvarInspecao');
//Route::get('/compliance/unidades/gerarInspecao/{id}', 'Correios\UnidadesController@gerarInspecao')->name('compliance.unidades.gerarInspecao');
//Route::put('/compliance/unidades/atualizar/{id}','Correios\UnidadesController@atualizar')->name('compliance.unidades.atualizar');
//Route::get('/compliance/unidades/editar/{id}', 'Correios\UnidadesController@edit')->name('compliance.unidades.editar');
//Route::post('/compliance/unidades{id?}', 'Correios\UnidadesController@search')->name('compliance.unidades.search');


/**
 * rotas para planejar inspeções in loco
 *
 */


Route::get('/compliance/planejamento', 'Correios\PlanejamentoController@index')->name('compliance.planejamento');
Route::get('/compliance/planejamento/edit/{id}', 'Correios\PlanejamentoController@edit')->name('compliance.planejamento.edit');
Route::post('/compliance/planejamento/update/{id}', 'Correios\PlanejamentoController@update')->name('compliance.planejamento.update');
Route::post('/compliance/planejamento/{search?}', 'Correios\PlanejamentoController@search')->name('compliance.planejamento.search');

//Route::post('/compliance/planejamento/create', 'Correios\PlanejamentoController@create')->name('compliance.planejamento.create');


/**
 * index – Lista os dados da tabela
 * show – Mostra um item específico
 * create – Retorna a View para criar um item da tabela
 * store – Salva o novo item na tabela
 * edit – Retorna a View para edição do dado
 * update – Salva a atualização do dado
 * destroy – Remove o dado
*/
