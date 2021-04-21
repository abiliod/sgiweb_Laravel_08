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
                    <a class="breadcrumb">Importação SNCI </a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card #dd2c00 deep-orange accent-4">
                <div class="card-content white-text">
                    <span class="card-title">Sistema: SNCI, bases anteriores </span>
                    <p>Grupo Tabelas Auxiliares</p>
                    <p>Função: Gestão de Reincidências</p>
                    <p>Assunto: Integridade de Inspeções</p>
                    <p></p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
                       Planilha disponibilizada pelo DCINT.  Edit a planilha deixando apenas as colunas a seguir:
                       [Modalidade	Diretoria	Código Unidade	Descrição da Unidade	Nº Inspeção	Nº Grupo	Descrição do Grupo	Nº Item	Descrição Item	Código REATE	ANO	Resposta	Comentário	Valor	CaracteresVlr	Falta	Sobra	EmRisco	DtUltAtu	DT_Inic Inspeção	DT_Fim Inspecao	Hora Inspecao	Situação	DT_Encerram	Status	Sigla do Status	Descrição do Status	DT_Posição]
                            Operações avançadas: no terminal inicie o serviço, digite o seguinte comando.
                            php artisan queue:work --queue=importacao
                            Cria um novo arquivo excel coloque aproximadamente 1000 linnhas por vez e agente o Job.
                            o serviço será inicializado  e o resultado esperado será por exemplo:
                            [2021-03-08 06:36:13][210] Processing: App\Jobs\JobSnci
                            [2021-03-08 10:19:30][210] Processed:  App\Jobs\JobSnci
                            [2021-03-08 10:19:31][211] Processing: App\Jobs\JobSnci

                        </textarea>
                    </div>
                    <form action="{{ route('compliance.importacao.snci') }}" method="POST" name="importform"
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

                            <a class="btn waves-effect waves-teal orange disabled" href="{{ url('/compliance/importacoes/snci/export') }}" disabled>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
