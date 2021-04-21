@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
    <h2 class="center">Importação</h2>
    <div class="row">
        <nav>
            <div class="nav-wrapper blue">
                <div class="col s12">
                    <a href="{{ route('home')}}" class="breadcrumb">Início</a>
                    <a href="{{ route('importacao')}}" class="breadcrumb">Importações</a>
                    <a class="breadcrumb">Importação PARÂMETROS_CADASTRAIS-ORGAOS-HORÁRIOS das Unidades</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">

            <div class="card  blue darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Dados Adicionais de Unidades</span>
                    <p>Unidades<br>Assunto: Cadastro de Dados Adicionais Unidades.</p>
                    <p>DERAT | Analítico - Para horário das Agências</p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                        <div class="input-field"  id ="ajuda" style="display:none;">
                            <i class="material-icons prefix">mode_edit</i>
                            <textarea  id="ajuda" name="ajuda" class="materialize-textarea">Relatório da TCO, GCAD - PARÂMETROS_CADASTRAIS-ORGAOS-HORÁRIOS.xlsx,  disponíveil no link: \\sac3063\INSTITUCIONAL\DIRAD\CECON\PUBLICO\GCAD\Relatórios_TCO.
                            </textarea>
                        </div>

                        <form action="{{ route('compliance.importacao.adicionalunidades') }}" method="POST" name="importform"
                            enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">
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
                                <a class="btn waves-effect waves-teal blue disabled" href="#" disabled>Export File
                                <i class="material-icons right">file_download</i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>
@endsection
