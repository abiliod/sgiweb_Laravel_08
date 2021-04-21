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
                    <a class="breadcrumb">Importação SMB X BDF - Proteção de Receita</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card #424242 grey darken-3">
                <div class="card-content white-text">


                    <span class="card-title">Sistema de Depósito Bancário</span>
                    <p>Função Prevenção de Perdas.</p>
                    <p>Assunto:  Integridade de Depósitos Bancários. SMB - BDF</p>
                    <p>Item: 270-3 - FINANCEIRO</p>

                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
                        Acessar o sistema SDE http://intranetsistemas/SDE/default.cfm
Opção Contabilização->Conciliação->Conciliação SMB x BDF.
Execute a consulta passando os parâmetros de data, período no mínimo 90 dias para sua Superintendência, ou grupo de superintendência de sua CVCO. Após a exibição da tela selecione a aba NÃO CONCILIADOS.
Na tela do SDE Estabeleça filtro para as unidades com Situação = "TODOS". Agrupamento por “MCU”.  Marque a partir do cabeçalho da tabela exibida até o final do relatório exceto a linha TOTAL DR: . Copiar e colar ESPECIAL em uma Planílha  excel.xlsx.
Frequencia: Diaria. (obs: fazer uma vez por semana deve-se fazer uma importação com período de 90 dias.)
Obs: COLAR ESPECIAL e fazer o tratamento dos dados para adequar ao Lay-out da planilha conforme disponibilizado.
Além da exclusão do cabeçalho quando da mudança de unidade é necessário fazer a exclusão de2 linhas geradas pelo sistema exportador a cada mudança de unidade para ficar apenas as informações relevantes para a importação.
Após o tratamento da planilha exclua a coluna (A) observe o lay-out.
VEJA o lay-out:
[MCU	Agencia	CNPJ	Data	SMBDinheiro	SMBCheque	SMBBoleto SMBEstorno	BDFDinheiro	BDFCheque	BDFBoleto	Divergencia	Status]
Tipo de importação: Por incremento. O Sistema ao importar a planilha grava apenas os registros não existentes que estão com status Pendente e atualizará os registros existentes com status atual encontrado na importação que por ventura estejam diferentes de Pendente. Em seguida irá apagará os registros com data de movimento maior que 120 dias.

                        </textarea>
                    </div>

                    <form action="{{ route('compliance.importacao.smb_bdf') }}" method="POST" name="importform"
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
                                <a class="btn waves-effect waves-teal blue disabled" href="{{ url('/compliance/importacoes/smb_bdf/export') }}" disabled>Export File
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
