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
                    <a class="breadcrumb">Importação Sistema: Encaminhamento</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
             <div class="card  #0d47a1 blue darken-4">
                <div class="card-content white-text">
                  <div class="card  #0d47a1 blue darken-4">

                  <span class="card-title">Sistema: Plano de Triagem <br>Encaminhamento</span>
                      <p>Grupo de Verificação 277</p>
                      <p>Função: Gestão de Mensagens Cie Eletronica</p>
                      <p>Assunto: Integridade de mensagens</p>
                      <p>
                          <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                          <label for="exibe">
                              <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                          </label>
                      </p>
                      <div class="input-field"  id ="ajuda" style="display:none;">
                          <i class="material-icons prefix">mode_edit</i>
                          <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
Sistema: Cie Eletrônica   http://apps2/sqm/app/index.php busque todas regionais e informe um período.
Fazer o download do período em planilha.xlsx
VEJA o lay-out:
[Número	Emissão	Origem	SE Origem	Destino	SE Destino	Irregularidade	Categoria	Número Objeto	Lida	Respondida	Fora do prazo	Data de resposta	Resposta]
Caso o processo seja interrompido por timeout fraguimente o  arquivo para um tamanho de 45 kb.
O sistema excluirá os lançamentos cujo a data do evento seja maior do que 1 ano e fará a persistêcia dos novos registros.
Frequencia: semanal.
                            </textarea>
                      </div>

                      <form action="{{ route('compliance.importacao.cieEletronica') }}" method="POST" name="importform"
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
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/cieEletronica/export') }}" disabled >Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
