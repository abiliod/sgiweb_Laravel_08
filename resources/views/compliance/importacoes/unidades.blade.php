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
                    <a class="breadcrumb">Importação Unidades</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">

            <div class="card  green darken-1 green darken-1">
                <div class="card-content white-text">
                        <span class="card-title">Unidades</span>
                        <p>Unidades<br>Assunto: Cadastro de Unidades.</p>
                        <p>Relatório ERP | R55001A.xlsx</p>
                        <p>Manter Cadastro de Unidades</p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                        <div class="input-field"  id ="ajuda" style="display:none;">
                            <i class="material-icons prefix">mode_edit</i>
                            <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
                                Imprima o Relatório ERP - R55001A Versão:	ECT0001  VEJA o lay-out:
                                [Unidades de Negócios	Nº Cad Geral	STO	DR	Descrição DR	Tipo do Órgão	Descrição Tp. órgão	Status do Órgão	Descrição Status	Descrição	Sigla	Nome Fantasia	Razão Social	CNPJ	Categoria	Tipo de Mecanização	Descrição do Tp. Mecanização	REVEN do Órgão	Descrição da REVEN	Faixa INI CEP	Faixa FIM CEP	Distribuição	Quantidade Guichês	Guichês Ocupados	Arquivo Externo?	Endereço	Possui Micro Administrativo?	Possui Máquina Backup?	Código INSS	Indicador INSS	Orgão Atendimento	Gerência/Área responsável	Categoria da localidade	Status CNPJ	Endereço	Complemento Endereço	Bairro	Cidade	Município	UF	CEP	Código IBGE do Município	DDD	Telefone Principal	Tipo de Telefone	Subordinação administrativa	Descrição Subordinação Adm.	Nome Responsável	Matrícula Responsável	Email da Unidade	Latitude	Longitude	Tipo de Estrutura	Período do Balancete Contábil	Contrato de Venda	Tamanho Físico	Termo de Convênio	Tipo de Agência de Correios	Ag Prestação de Contas	Inscrição Estadual	Subordinação Tecnica	Unidade Primaria	Descrição Und. Primaria	Código IBGE Distrito]
                                Utilize uma planilha de apoio por nome de  R55001A.xlsx, Mantenha a Linha de cabeçalho.
                                Copiar os dados da planilha original, colar salvar em seguida marque para importar. O Sistema incrementa atualiza essa planilha máximo 1000 linhas.
                                A opção Exportar está desabilitada por questões de segurança empresarial.
                                Frequencia: Início do ciclo.

                                </textarea>
                        </div>

                        <form action="{{ route('compliance.importacao.unidades') }}" method="POST" name="importform"
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
