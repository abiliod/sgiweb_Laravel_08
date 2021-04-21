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
                    <a class="breadcrumb">Importação Sistema: Gestão de Recursos SRO</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
             <div class="card  #424242 grey darken-3">
                <div class="card-content white-text">
                  <div class="card  #424242 grey darken-3">
                  <span class="card-title">Sistema: Gestão da Distribuição Domiciliaria </span>
                      <p>Grupo de Verificação 277</p>
                      <p>Função: Gestão de Recursos SRO</p>
                      <p>Assunto: Objetos Não Entregues Primeira Tentativa</p>
                      <p>
                          <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                          <label for="exibe">
                              <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                          </label>
                      </p>
                      <div class="input-field"  id ="ajuda" style="display:none;">
                          <i class="material-icons prefix">mode_edit</i>
                          <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
Sistema: MicroEstrategy, http://dwcorreios/microstrategy/servlet/mstrWeb
Pesquisar  Superintendencia, importar para uma planilha formato excel.xlsx
Após o download da tabela  importar para o Excel e higienizar a planilha deixando apenas os objetos cujo a coluna Código do Evento seja igual a BDE.
Exclua também a ultima linha, linha de totalização gerada pelo sistema.
VEJA o lay-out:
[DR de Destino	Nome da Unidade	Código do Objeto	Descrição do Evento	Código do Evento	Data do Evento	Qtde Objetos NÃO Entregues na Primeira Tentativa]
Caso o processo seja interrompido por timeout fraguimente o  arquivo para um tamanho de 45 kb.
O sistema excluirá os lançamentos cujo a data do evento seja maior do que 210 dias e fará a persistêcia dos novos registros.
Frequencia: diária.
                            </textarea>
                      </div>

                    <form action="{{ route('compliance.importacao.microStrategy') }}" method="POST" name="importform"
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
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/microStrategy/export') }}" readonly>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
