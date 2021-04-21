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
                    <a class="breadcrumb">Importação Cadastral</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card #004d40 teal darken-4">
                <div class="card-content white-text">
                    <span class="card-title">Cadastral</span>
                    <p>Cadastro do Efetivo<br>Assunto: Auxiliar do Sistema.</p>
                    <p>WebSGQ 3 - Efetivo analitico por MCU | WebSGQ 3 - Efetivo analitico por MCU-SE.xlsx</p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
                                Imprima o Relatório WebSGQ 3 - Efetivo analitico por MCU VEJA o lay-out:
                                [SE/CS	Matrícula	Nome	Lotação	MCU	Cargo	Especialidade	Função	sexo]
                                Sistema: http://intranetmg2/WebSGQ3/principal.asp MENU consulta->efetivo->analitico por mcu .
                                Pesquisar  Superintendencia, selecionar a SE interessada marcar incluir orgãos subordinados, na caixa de rolagem selecione a opção sexo em seguida importar para o excel.
                                Ajuste o cabeçalho da planilha para o lay out indicado. Ao salvar  renomeie para sua Regional  ex de nome: (WebSGQ 3 - Efetivo analitico por MCU-GO.xlsx).
                                Frequencia: MENSAL.
                        </textarea>
                    </div>

                    <form action="{{ route('compliance.importacao.cadastral') }}" method="POST" name="importform"
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
                                <a class="btn waves-effect waves-teal blue disabled" href="{{ url('/compliance/importacoes/cadastral/export') }}" disabled>Export File
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
