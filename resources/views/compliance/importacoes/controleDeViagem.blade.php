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
                    <a class="breadcrumb">Importação Sistema: Atendimento Comercial</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
             <div class="card #424242 grey darken-3">
                <div class="card-content white-text">
                  <div class="card  #424242 grey darken-3">

                  <span class="card-title">Sistema: Movimentação de Carga Postal</span>
                    <p>Grupo/Item: 276.1, Função: Controle de viagem Apontamentos
                      <p>Grupo de Verificação 276</p>
                      <p>Assunto: Controle de viagem Apontamentos</p>
                      <p>Verificação do Apontamento de Embarque/Desembarque</p>
                      <p>
                          <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                          <label for="exibe">
                              <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                          </label>
                      </p>
                      <div class="input-field"  id ="ajuda" style="display:none;">
                          <i class="material-icons prefix">mode_edit</i>
                          <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
                            No Sistema ERP exporte para uma planilha dados da aba embarque e desembarque da sua regional.
                            Parâmetros datas de início e final da viagem.  status v1 até v9 . exclua da planilha todos registros que estiver com a  Data Chegada Prevista igual nulo ou vazio.
                            O sistema irá guardar histórico de 180 dias.
                            Frequencia: semanal.
                            </textarea>
                      </div>

                    <form action="{{ route('compliance.importacao.controleDeViagem') }}" method="POST" name="importform"
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
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/controleDeViagem/export') }}" disabled>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
