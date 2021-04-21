@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
    <h2 class="center">Importação</h2>
    <div class="row">
        <nav>
            <div class="nav-wrapper green">
                <div class="col s12">
                    <a href="{{ route('home')}}" class="breadcrumb">Início</a>
                    <a href="{{ route('importacao')}}" class="breadcrumb">Importações</a>
                    <a class="breadcrumb">Importação Sistema: Pré Alerta</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
             <div class="card #b71c1c red darken-4">
                <div class="card-content white-text">
                  <div class="card #b71c1c red darken-4">

                  <span class="card-title">Sistema: Distribuição Domiciliária <br>Pré Alerta</span>
                      <p>Grupo de Verificação 277</p>
                      <p>Função: Gestão de Recursos SRO Painel de Extravio</p>
                      <p>Assunto: Conferência Eletrônica</p>
                      <p>
                          <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                          <label for="exibe">
                              <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                          </label>
                      </p>
                      <div class="input-field"  id ="ajuda" style="display:none;">
                          <i class="material-icons prefix">mode_edit</i>
                          <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
Painel de extravios,    http://paineldeextravios.correiosnet.int – MENU:Folha de Selecione a Regional, na aba Relatórios Operacionais  ->Trechos Totais, informe o período. Fazer o download do período
VEJA o lay-out:
[objeto	data_evento	evento	cliente	trecho	evento_trecho	unid_origem	unid_destino	dr_origem	dr_destino	gestao_prealerta	automatico	manual	total	macroprocesso	postado	ultimo_evento_extraviado	ultimo_evento_em_transito	ultimo_evento	ultimo_evento_data	evento_finalizador	tipo	analise_sro	unid_origem_apelido	unid_destino_apelido	trecho_real	se_postagem	unidade_postagem	data_postagem	familia	ultimo_evento_sinistro]
Caso o processo seja interrompido por timeout fraguimente o  arquivo para um tamanho aproximado de 350 kb.
O sistema excluirá os lançamentos cujo a data do evento seja maior do que 210 dias e fará a persistêcia dos novos registros.
Frequencia: Semanal.
                            </textarea>
                      </div>


                      <form action="{{ route('compliance.importacao.painelExtravio') }}" method="POST" name="importform"
                            enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="file-field input-field">
                            <div class="btn">
                              <span>File</span>
                              <input type="file" name="file">
                            </div>
                            <div class="file-path-wrapper">
                              <input class="file-path validate" type="text">
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="btn waves-effect waves-teal"
                                 type="submit" name="action">Import File
                                <i class="material-icons right">file_upload</i>
                            </button>
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/painelExtravio/export') }}">Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
