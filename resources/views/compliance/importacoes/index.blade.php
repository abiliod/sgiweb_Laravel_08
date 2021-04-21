@extends('layouts._sgiweb.app')

@section('content')
<div class="container">
<h2 class="center">Importações Disponíveis</h2>
<div class="row">
     <nav>
        <div class="nav-wrapper green">
              <div class="col s12">
                <a href="{{ route('home')}}" class="breadcrumb">Início</a>
                <a class="breadcrumb">Compliance Importações</a>
              </div>
        </div>
      </nav>
</div>
<div class="row">
        @can('importar_unidades')
            <div class="col s12 m6">
                <div class="card green darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Unidades</span>
                        <p>Unidades<br>Assunto: Cadastro de Unidades.</p>
                        <p>Relatório ERP | R55001A.xlsx</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.unidades')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_unidades')
            <div class="col s12 m6">
                <div class="card blue darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Dados Adicionais de Unidades</span>
                        <p>Unidades<br>Assunto: Cadastro de Dados Adicionais Unidades.</p>
                        <p>GCAD | PARÂMETROS_CADASTRAIS-ORGAOS-HORÁRIOS.xlsx</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.adicionalunidades')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_bdSNCI')
            <div class="col s12 m6">
                <div class="card #dd2c00 deep-orange accent-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: SNCI, bases anteriores </span>
                        <p>Grupo Tabelas Auxiliares</p>
                        <p>Função: Gestão de Reincidências</p>
                        <p>Assunto: Integridade de Inspeções</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.snci')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_cadastral')
            <div class="col s12 m6">
                <div class="card #004d40 teal darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Cadastral</span>
                        <p>Cadastro do Efetivo<br>Assunto: Auxiliar do Sistema.</p>
                        <p>WebSGQ 3 - Efetivo analitico por MCU</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.cadastral')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_feriados')
            <div class="col s12 m6">
                <div class="card blue darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Feriados</span>
                         <p>Cadastro de Feriados<br>Assunto: Auxiliar do Sistema.</p>
                         <p>Manter cadastro de feriados.</p>

                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.feriado')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_webCont')
            <div class="col s12 m6">
                <div class="card   orange darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema WebCont</span>
                        <p>Função: Prevenção de Perdas.</p>
                        <p>Assunto: Débito de Empregado: Conta  11202.994000</p>
                        <p>Item: 270-1-FINANCEIRO-WebCont_DebitoEmpregado.xlsx<br/></p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.webcont')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_proter')
            <div class="col s12 m6">
                <div class="card #0d47a1 blue darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema Proter</span>
                        <p>Função Prevenção de Perdas.</p>
                        <p>Assunto: Proteção de Receitas. PROTER</p>
                        <p>Item: 270-2 - FINANCEIRO </p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.proter')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_smbBdf')
            <div class="col s12 m6">
                <div class="card #424242 grey darken-3">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema de Depósito Bancário</span>
                        <p>Função Prevenção de Perdas.</p>
                        <p>Assunto:  Integridade de Depósitos Bancários. SMB - BDF</p>
                        <p>Item: 270-3 - FINANCEIRO</p>
                        <p></p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.smb_bdf')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_sld02Bdf')
            <div class="col s12 m6">
                <div class="card #b71c1c red darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Gestão de Numerário</span>
                        <p>Grupo de Verificação 270, Função Prevenção de Perdas.</p>
                            <p>Assunto: Saldo que passa</p>
                            <p>Item: 270-4 - FINANCEIRO</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.SL02_bdf')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_respDefinida')
            <div class="col s12 m6">
                <div class="card #dd2c00 deep-orange accent-4">
                    <div class="card-content white-text">
                        <span class="card-title">Segurança Postal</span>
                        <p>Grupo de Verificação 271</p>
                        <p>Função: Processos Administrativos</p>
                        <p>Assunto: Responsabilidade Definida</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.RespDefinida')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_alarme')
            <div class="col s12 m6">
                <div class="card blue darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema de Alarme</span>
                        <p>Grupo de Verificação 272.</p>
                        <p>Função: Gestão do Alarme Monitorado</p>
                        <p>Assunto: Ativação / Desativação</p>
                        <p></p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.alarme')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_frequenciaPorSE')
            <div class="col s12 m6">
                <div class="card #0d47a1 blue darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema Segurança</span>
                        <p>Grupo de Verificação 272</p>
                        <p>Assunto: Compartilhamento de Senhas Alarme Monitorado</p>
                        <p>WebSGQ3 - Frequencia por SE</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.absenteismo')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_cftv')
            <div class="col s12 m6">
                <div class="card #b71c1c red darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema Segurança Patrimonial</span>
                        <p>Grupo de Verificação 272</p>
                        <p>Assunto: Gestão do Funcionamento do equipamento CFTV</p>
                        <p>Qualidade do Funcionamento</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.cftv')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_feriasPorMcu')
            <div class="col s12 m6">
                <div class="card deep-orange">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema Segurança</span>
                        <p>Grupo de Verificação 272</p>
                        <p>Assunto: Compartilhamento de Senhas Alarme Monitorado</p>
                        <p>WebSGQ3 - Fruicao de ferias por MCU</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.ferias')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_PLPs')
            <div class="col s12 m6">
                <div class="card deep-purple">

                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Atendimento Comercial</span>
                        <p>Grupo de Verificação 274 ListasPendentes PLPs</p>
                        <p>Assunto: Condições de Aceitação, Classificação e Tarifação de Objetos</p>

                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.plpListaPendente')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_controleViagem')
            <div class="col s12 m6">
                <div class="card #424242 grey darken-3">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Movimentação de Carga</span>
                        <p>Grupo de Verificação 276</p>
                        <p>Assunto: Controle de viagem Apontamentos</p>
                        <p>Verificação do Apontamento de Embarque/Desembarque</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.controleDeViagem')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_sgdo')
            <div class="col s12 m6">
                <div class="card green darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Distribuição Domiciliária <br>SGDO</span>
                        <p>Grupo de Verificação 277</p>
                        <p>Assunto: Lançamentos Obrigatórios SGDO</p>
                        <p>Verificação do Lançamentos  SGDO</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.sgdoDistribuicao')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan
        @can('importar_microStrategy')
            <div class="col s12 m6">
                <div class="card  #424242 grey darken-3">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Gestão da Distribuição Domiciliaria</span>
                        <p>Grupo de Verificação 277</p>
                        <p>Função: Gestão de Recursos SRO</p>
                        <p>Assunto: Objetos Não Entregues Primeira Tentativa</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.microStrategy')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan
        @can('importar_painelExtravio')
            <div class="col s12 m6">
                <div class="card #b71c1c red darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Distribuição Domiciliária <br>Pré Alerta</span>
                        <p>Grupo de Verificação 277</p>
                        <p>Função: Gestão de Recursos SRO Painel de Extravio</p>
                        <p>Assunto: Conferência Eletrônica</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.painelExtravio')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_cieEletronica')
            <div class="col s12 m6">
                <div class="card #0d47a1 blue darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Plano de Triagem <br>Encaminhamento</span>
                        <p>Grupo de Verificação 277</p>
                        <p>Função: Gestão de Mensagens Cie Eletronica</p>
                        <p>Assunto: Integridade de mensagens</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.cieEletronica')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_pagamentosAdicionais')
            <div class="col s12 m6">
                <div class="card #004d40 teal darken-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Gestão de Recursos Humanos</span>
                        <p>Grupo de Verificação 278</p>
                        <p>Função: Gestão de Recursos Humanos</p>
                        <p>Assunto: Integridade de pagamentos</p>

                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.pagamentosAdicionais')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan

        @can('importar_bdfFat_02')
            <div class="col s12 m6">
                <div class="card #dd2c00 deep-orange accent-4">
                    <div class="card-content white-text">
                        <span class="card-title">Sistema: Gestão de Recursos Humanos </span>
                        <p>Grupo de Verificação 278  BDF_FAT_02</p>
                        <p>Função: Gestão de Recursos Humanos</p>
                        <p>Assunto: Integridade de pagamentos</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('importacao.bdf_fat_02')}}">Importar Planilha</a>
                    </div>
                </div>
            </div>
        @endcan
            @can('importar_cieEletronica')
{{--                Mario depois vc configura a segurança correta  pois está para importar_cieEletronica Obrigado, Abilio--}}
                <div class="col s12 m6">
                    <div class="card #0d47a1 blue darken-4">
                        <div class="card-content white-text">
                            <span class="card-title">Sistema: Plano de Encaminhamento da Carga <br></span>
                            <p>Grupo </p>
                            <p>Função: Gestão </p>
                            <p>Assunto: Integridade das Unidades e da Carga</p>
                        </div>
                        <div class="card-action">
                            <a class="white-text" href="{{route('importacao.apontamentoCV')}}">Importar Planilha</a>
                        </div>
                    </div>
                </div>
            @endcan



</div>
@endsection
