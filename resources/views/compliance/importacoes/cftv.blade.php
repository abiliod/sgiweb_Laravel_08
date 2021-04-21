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
                    <a class="breadcrumb">Importação Evenntos - Por Unidades e Usuário</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
             <div class="card #b71c1c red darken-4">
                <div class="card-content white-text">
                  <div class="card #b71c1c red darken-4">

                <span class="card-title">Sistema Segurança Patrimonial</span>
                      <p>Grupo de Verificação 272</p>
                      <p>Assunto: Gestão do Funcionamento do equipamento CFTV</p>
                      <p>Qualidade do Funcionamento</p>

                      <p>
                          <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                          <label for="exibe">
                              <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                          </label>
                      </p>
                      <div class="input-field"  id ="ajuda" style="display:none;">
                          <i class="material-icons prefix">mode_edit</i>
                          <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
                            Importar os dados dos CFTVs de uma planilha formato xlsx.
                            VEJA o lay-out:
                            [MCU	unidade	cameras_fixa_cf	cameras_infra_vermelho_cir	dome	modulo_dvr	no_break	hack	pc_auxiliar	portaweb	end_ip	link	user	password	port	marcamodelo	statusconexao	data_ultima_conexao	observacao	data_no_equipamento	hora_no_equipamento]
                            Tipo de importação: Por incremento.
                            O Sistema ao importar a planilha grava novos registrow e atualia os registros existentes
                          </textarea>
                      </div>
                    <form action="{{ route('compliance.importacao.cftv') }}" method="POST" name="importform"
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
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/cftv/export') }}"disabled>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
