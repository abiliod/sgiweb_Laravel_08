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
                    <a class="breadcrumb">Importação Controle de Viagem  - Apontamento de Viagem</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card #42a5f5 blue lighten-1">
                <div class="card-content white-text">

                    <span class="card-title">Sistema: Plano de Encaminhamento da Carga <br></span>
                    <p>Grupo </p>
                    <p>Função: Gestão </p>
                    <p>Assunto: Integridade das Unidades e da Carga</p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
Acessar o Sistema ERP, função Gestão de Linha de Transportes, no programa Desempenho das linhas de transportes.
Como parâmetro informar apenas um unico dia da semana entre segunda e sexta feira para os parametros datas inicio e final da viagem.
Coloque status V1 e V5 informe a SE. Clicar em procurar e exibir todas linhas na tela. em seguida exporte para excel.
Executar esse processo para todas SEs e todos tipos de linhas exceto LTNs.
Após o download do arquivo abrir e salvar como pasta de trabalho excel para que o mesmo passe a ter a extensão xlsx.
                        </textarea>
                    </div>


                    <form action="{{ route('compliance.importacao.apontamentoCV') }}" method="POST" name="importform"
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

                            <a class="btn waves-effect waves-teal orange disabled" href="{{ url('/compliance/importacoes/alarme/export') }}" disabled>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
