<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <style>
        /**
            Set the margins of the page to 0, so the footer and the header
            can be of the full height and width !
         **/
        @page {
            margin: 0cm 0cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 5cm;
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 5cm;

            /** Extra personal styles **/
            background-color: #03a9f4;
            color: white;
            text-align: center;
            line-height: 1.5cm;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;

            /** Extra personal styles **/
            background-color: #03a9f4;
            color: white;
            text-align: center;
            line-height: 5cm;
        }

    </style>
</head>
<body>
<!-- Define header and footer blocks before your content -->
<header>
    <h2 class="center">Papel de Trabalho</h2>
    <h6 class="float-right">Ciclo: {{ $inspecao->ciclo }}. / Unidade: {{ $inspecao->descricao }}.</h6>
    <h6 class="float-right">Tipo Verificação : {{ $inspecao->tipoVerificacao }}. /   Período do Relatório : {{ \Carbon\Carbon::parse($inspecao->datainiPreInspeção)->format( 'd/m/Y' ) }}.</h6>
    <h6 class="float-right">Número do Relatório : {{ $inspecao->codigo }}. / Status da Inspeção : {{ $inspecao->status }}.</h6>

</header>

<footer>
    Copyright By Abilio.adm.br &copy; <?php echo date("Y");?>
</footer>

<!-- Wrap the content of your PDF inside a main tag -->
<main>


    <table class="row">
        <thead>
        <tr>
            <th>
                <h6 class="center">Oportunidade de Aprimoramento e Orientações</h6>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($registros as $registro)
            <tr>
                <td>
                    <b>Grupo:</b>  {{ $registro->numeroGrupoVerificacao }} {{ $registro->nomegrupo }}
                    <b>Item - {{ $registro->numeroDoTeste }} :</b> {{ $registro->teste }}

                    <br><br> <b>Avaliação:</b> {{ $registro->avaliacao }}

                    <br> <b>Oportunidades de aprimoramento/Metodo de Avaliação:</b>
                    <br>{{ $registro->oportunidadeAprimoramento }}
                    @if ($registro->avaliacao == 'Não Conforme')
                        <br>
                        <br>
                        @if (!empty($registro->evidencia ))

                            {{ $registro->evidencia }}

                        @endif
                    @endif

                    <br><br><b>Norma:</b> {{ $registro->norma }}

                    @if ($registro->avaliacao == 'Não Conforme')
                        <br><b>Possiveis consequências:</b>
                        <br>{{ $registro->consequencias }} <br> <br><br>
                    @endif

                    @if (($registro->avaliacao == 'Não Conforme') && ( $registro->itemQuantificado == 'Sim'))
                        <br><b>Item Quantificado.</b>

                        <br><b>Valores:</b> Falta : {{ isset($registro->valorFalta) ?  'R$'.number_format($registro->valorFalta, 2, ',', '.') : 'R$ 0,00' }} Sobra: {{ isset($registro->valorSobra) ?  'R$'.number_format($registro->valorSobra, 2, ',', '.') : 'R$ 0,00' }} Em Risco: {{ isset($registro->valorRisco) ?  'R$'.number_format($registro->valorRisco, 2, ',', '.') : 'R$ 0,00' }}
                    @endif

                    @if (($registro->avaliacao == 'Não Conforme') && ( $registro->reincidencia == 'Sim'))

                        <br><b>Item Reincidente.</b>
                        <br><b>Nº Relatório:</b>
                        {{ isset($registro->codVerificacaoAnterior) ?  $registro->codVerificacaoAnterior : '' }} - Nº Grupo: {{ isset($registro->numeroGrupoReincidente) ?  $registro->numeroGrupoReincidente : '' }} - Nº Item:  {{ isset($registro->numeroItemReincidente) ?  $registro->numeroItemReincidente : '' }}
                        <br> <br><br>
                    @endif

                    @if ($registro->avaliacao == 'Não Conforme')
                        <div class="row col s12">
                        <b>Orientações:</b>
                        <br>{{ $registro->orientacao }} <br> <br><br>

                        </div>
                        @if(!empty($registro->diretorio))
                        <div class="row col s12">
                          <br> <br><br> <b>Anexo:</b> <br> <br><br>
                        </div>
                            @foreach(File::glob(($registro->diretorio.'*.*')) as $imagem)
                                <div class="row col s12">
                                    <img style="display:inline;" width="120"  src="{{asset( $imagem )}}">
                                </div>
                            @endforeach
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach

        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
        </tbody>
    </table>
</main>

</body>
</html>
