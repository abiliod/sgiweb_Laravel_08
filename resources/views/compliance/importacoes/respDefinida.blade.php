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
                    <a class="breadcrumb">Importação Responsabilidade Definida</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card #dd2c00 deep-orange accent-4">
                <div class="card-content white-text">

                <span class="card-title">Segurança Postal</span>
                    <p>Grupo de Verificação 271</p>
                    <p>Função: Processos Administrativos</p>
                    <p>Assunto: Responsabilidade Definida</p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
Importar os dados condensados das planilhas Responsabilidade definidas para uma planilha formato xlsx.
As planilhas tem lay-outs diferentes unifique para ficar conforme o modelo a seguir.
VEJA o lay-out:
[Codigo	UNIDADE	data_Pagamento	Objeto	DataPostagem	Serviço_Produto	Valor da Indenização	STO	mcu	unidade	subordinacao	nu_PedidoInformacao	se_pagadora	data	nu_sei	nu_sei_AbertoUnidade	situacao	data	empregadoResponsavel	observacoes	conclusao	providenciaAdotada]
Tipo de importação: Por Atualização.
O Sistema ao importar a planilha atualiza os registros existentes e inclui os registros não existentes.

                        </textarea>
                    </div>
                    <form action="{{ route('compliance.importacao.RespDefinida') }}" method="POST" name="importform"
                            enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">

                        </div>

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
                                <a class="btn waves-effect waves-teal blue" href="{{ url('/compliance/importacoes/RespDefinida/export') }}" disabled>Export File
                                <i class="material-icons right">file_download</i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
