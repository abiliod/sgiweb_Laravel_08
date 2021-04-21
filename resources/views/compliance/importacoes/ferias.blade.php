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
                    <a class="breadcrumb">Importação Ferias - Ferias Por Unidades</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card deep-orange">
                <div class="card-content white-text">

                <span class="card-title">Sistema Segurança</span>
                    <p>Grupo de Verificação 272</p>
                    <p>Assunto: Compartilhamento de Senhas Alarme Monitorado</p>
                    <p>WebSGQ3 - Fruicao de ferias por MCU</p>

                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
AM 00434111
BSB 00434113
GO 00434116
PA 00434123
RR 00434131
RO 00434122
TO 00434135

Fruicao de ferias por MCU
Sistema: http://intranetmg2/WebSGQ3/principal.asp MENU Férias -> Fruição por MCU.
Pesquisar  Superintendencia, selecionar a SE interessada ex:[00434122] marcar incluir orgãos subordinados, em seguida fazer o download utilize o excel do download.

Frequencia: MENSAL.
Pré requisito: Cadastral deve estar atualizada; caso o processo seja interrompido por timeout fraguimente o arquivo tomado cuidado para não dividir registros de mesma matrícula em duas importações, dado que o empregado pode programar a férias para dois períodos distintos.
O sistema exclui por unidade a programação das férias e cria novos registros.
VEJA o lay-out:
[Matrícula	Nome	Lotação	Função	Início fruição	Término fruição	Dias	Saldo	Abono]
Frequencia: MENSAL. É necessário que os ultimos 12 meses sejam importados para o sistema.
                        </textarea>
                    </div>

                    <form action="{{ route('compliance.importacao.ferias') }}" method="POST" name="importform"
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
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/ferias/export') }}"disabled>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
