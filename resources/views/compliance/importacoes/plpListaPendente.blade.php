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
             <div class="card deep-purple">
                <div class="card-content white-text">
                  <div class="card  deep-purple">

                <span class="card-title">Sistema: Atendimento Comercial</span>
                      <p>Grupo de Verificação 274 ListasPendentes PLPs</p>
                      <p>Assunto: Condições de Aceitação, Classificação e Tarifação de Objetos</p>
                      <p>
                          <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                          <label for="exibe">
                              <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                          </label>
                      </p>
                      <div class="input-field"  id ="ajuda" style="display:none;">
                          <i class="material-icons prefix">mode_edit</i>
                          <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
Importar os dados PLPs pendentes.
Solicitar à área de atendimento (Suporte SARA) a relação atualizada de PLP - LISTAS Pendentes, formato Excel.xlsx.
ou fazer o download de em \\sac3063\INSTITUCIONAL\DIOPE\DERAT\PUBLICO\GMAT_pub\LISTA_PENDENTE
VEJA o lay-out:
[DR	STO/MCU	NOME_AGÊNCIA	LISTA	PLP	OBJETO	CLIENTE	DH LISTA POSTAGEM]
Tipo de importação: Por substituição.
Frequencia: Mensal o arquivo disponibilizado  no sac contém de todas Regionais.
O Sistema ao importar a planilha grava novos registros e exclui registros antigos.
                          </textarea>
                      </div>
                    <form action="{{ route('compliance.importacao.plpListaPendente') }}" method="POST" name="importform"
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
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/plpListaPendente/export') }}" disabled>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
