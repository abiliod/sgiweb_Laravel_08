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
                    <a class="breadcrumb">Importação Feriado - Feriados Por Municipio</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card blue darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Sistema de Feriados</span>
                    <p>Cadastro do Feriados<br>Assunto: Auxiliar do Sistema.</p>
                    <p>Relatório Feriado ERP | Feriado.xlsx</p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
                                Imprima o Relatório ERP: Cadastro Geral -> Feriados -> Relatório de Feriados.
                                Submeter -> Impressora  -> OK, aguarde o processamento em seguida.
                                Relatório de Feriados Exib. saída.
                                Relatório Feriados VEJA o lay-out:
                                [UF	Nome Município	Tipo Feriado	Descrição Feriado	Data do Feriado]
                                Frequencia: INÍCIO DO CICLO.
                                Se o sistema der erro TIME-OUT, divida a planilha para que a mesma tenha uma quantidade menor de registros.
                        </textarea>
                    </div>

                    <form action="{{ route('compliance.importacao.feriado') }}" method="POST" name="importform"
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

                            <a class="btn waves-effect waves-teal orange disabled" href="{{ url('/compliance/importacoes/feriado/export') }}" disabled >Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
