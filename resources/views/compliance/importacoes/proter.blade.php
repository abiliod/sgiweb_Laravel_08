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
                    <a class="breadcrumb">Importação Proter - Proteção de Receita</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
            <div class="card #0d47a1 blue darken-4">
                <div class="card-content white-text">
                    <span class="card-title">Sistema Proter</span>
                    <p>Função Prevenção de Perdas.</p>
                    <p>Assunto: Proteção de Receitas. PROTER</p>
                    <p>Item: 270-2 - FINANCEIRO </p>
                    <p>
                        <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                        <label for="exibe">
                            <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                        </label>
                    </p>
                    <div class="input-field"  id ="ajuda" style="display:none;">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
As informações são obtidas por meio: Sistema SPN->Itens Monitorados->Objeto Postal->Relatório Pendências Geral. Efetuar a pesquisa a partir de Janeiro/2017.
Fazer o download no formato(CSV).
Importar os dados para uma Planilha formato xlsx.  Abrir o Arquivo excluir a primeira linha.
Na planilha aplicar Filtro na coluna [TIPO DE PENDÊNCIA], desmarque as opções CON e DPC Click em OK. Em seguida exclua todas linhas cujo o tipo de pendência não seja CON e DPC, em seguida desative o filtro e salve o arquivo.

Manteha sempre esse lay-out observe as palavras quando da importação faça o (ajuste ou utilize o cabeçalho do arquivo modelo): [tipo_de_pendencia status_da_pendencia    data_da_pendencia  divergencia_peso   divergencia_cep    origem_pendencia   SE TIPO DE UNIDADE    STO/MCU    NOME DA UNIDADE    STATUS DA UNIDADE  TIPO DE ATENDIMENTO    matricula_atendente    no_do_objeto   DATA DA POSTAGEM   DATA DA ENTREGA    codigo_do_servico  CEP CONTABILIZADO (SARA)   CEP ENTREGA SRO    PESO TARIFADO FINANCEIRO   COMPRIMENTO FINANCEIRO LARGURA FINANCEIRO ALTURA FINANCEIRO  peso_cubico_financeiro PESO REAL MECTRI   COMPRIMENTO MECTRI LARGURA MECTRI ALTURA MECTRI  peso_cubico_mectri PESO TARIFADO MECTRI   VALOR TARIFADO FINANCEIRO  VALOR TARIFADO MECTRI  diferenca_a_recolher   CNPJ DO CLIENTE    CONTRATO   cartao_postagem    NOME DO CLIENTE    QTD DUPLICIDADES   ÚLTIMA MANIFESTAÇÃO    MCU Triagem   Centro Peso   Volume Altura Largura    Comprimento    Data de leitura    Tipo do objeto Cep destino    Tipo de indução    Número da máquina  Código da estação]

Sugestão de nome do Arquivo modelo lay-out: 270-2-FINANCEIRO-Proter_ProtecaoReceita.xlsx .

Frequencia: Mensal/Quinzenal.(obs: Há possibilidade de uma pendência em anos anteriores ter sido regularizada recentemente.)Se o sistema der erro TIME-OUT, diminua a quantidade de registros na planilha. Ou coloque a planilha com menos guias de planilhas.

                        </textarea>
                    </div>


                    <form action="{{ route('compliance.importacao.proter') }}" method="POST" name="importform"
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
                                <a class="btn waves-effect waves-teal blue disabled" href="{{ url('/compliance/importacoes/proter/export') }}" disabled>Export File
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
