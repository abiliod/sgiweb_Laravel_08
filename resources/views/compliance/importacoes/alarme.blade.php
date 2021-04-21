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
                    <a class="breadcrumb">Importação Alarme - Arme/Desarme</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card #42a5f5 blue lighten-1">
                <div class="card-content white-text">
                    <span class="card-title">Sistema de Alarme</span>
                    <p>Grupo de Verificação 272.</p>
                    <p>Função: Gestão do Alarme Monitorado</p>
                    <p>Assunto: Ativação / Desativação</p>
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
r o Sistema http://10.120.9.38/rel, informe a SE o período, marque todas unidades e selecione a opção ambos.
Importar os dados do relatório de alarme monitorado ARME/DESARME para uma planilha formato xlsx.
marque a partir do cabeçalho da pagina até a ultima linha copie e cole na planilha (utilize a opção colar valores do excel)
VEJA o lay-out:
[CLIENTE	        MCU	USUÁRIO	MATRÍCULA	ARME/DESARME	DATA	      HORA]
[AC MATRINCHÃ - GO	6866	1	8332425- 9	Desarme 	26/10/2020   08:19:05] -> mantenha essa formatação de data e hora na planilha
Tipo de importação: Por incremento.O Sistema ao importar a planilha grava os registros não existentes Em seguida irá apagará os registros existentes na tabela com data  maior que 12 meses.
                        </textarea>
                    </div>


                    <form action="{{ route('compliance.importacao.alarme') }}" method="POST" name="importform"
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
