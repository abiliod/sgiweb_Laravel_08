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
                    <a class="breadcrumb">Importação Sistema: Gestão de Recursos Humanos</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col s12 m12">
             <div class="card #dd2c00 deep-orange accent-4">
                <div class="card-content white-text">
                  <div class="card #dd2c00 deep-orange accent-4">
                  <span class="card-title">Sistema: Gestão de Recursos Humanos <br>Recebimentos</span>
                      <p>Grupo de Verificação 278 BDF_FAT_02</p>
                      <p>Função: Gestão de Recursos Humanos</p>
                      <p>Assunto: Integridade de pagamentos</p>
                      <p>
                          <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
                          <label for="exibe">
                              <span class="card-content orange-text">Exibir Ajuda do Item?</span>
                          </label>
                      </p>
                      <div class="input-field"  id ="ajuda" style="display:none;">
                          <i class="material-icons prefix">mode_edit</i>
                          <textarea  id="ajuda" name="ajuda" class="materialize-textarea">
BDF-FAT-02, a importação deve ser por demanda de inspeção.
Isto é, apenas as unidades a serem inspecionadas. Acessar o sistema BDF importar o relatório das unidades a ser inspecionadas para um arquivo txt e posteriormente para uma planilha formato xlsx. Uma boa margem de segurança importe 4 meses.
1 - Para utilizar importação via sistema, Importe para uma guia de planilha do arquivo template e apague a guia anterior de modo ficar apenas os dados recém importados. Cole o cabeçalho da planilha anterior  na planilha dos dados recém importados. Salve a planilha e execute a importação. (Importação por Incremento, O sistema importa a planilha e apaga as informações com mais de 210 dias corridos.); Obs: após a planilha montada verifique, a planilha deve ter no máximo 1.500 kb.  Ou monte SQL conforme modelo.
VEJA o lay-out:
DR    CD_ORGAO   ORGAO  AG_POSTAGEM    DT_POSTAGEM    ETIQUETA   SERVICO    VLR_MEDIDA CD_GRUPO_PAIS_DESTINO  CEP_DESTINO    VLR_COBRADO_DESTINATARIO   VLR_DECLARADO  COD_ADM    PRODUTO    QTDE_PRESTADA  VLR_SERVICO    VLR_DESCONTO   ACRESCIMO  VLR_FINAL  CARTAO DOCUMENTO  servio_adicional NOME_SERVICO  CONTRATO   ATENDIMENTO    DT_MOV]
Frequencia: POR DEMANDA DA INSPEÇÃO.
O arquivo deve ter aproximadamente 1000 linhas 130kb.
2 - Para utilizar o modo SQL, deve fazer conversão de datas para que as mesmas fiquem no padrão Mysql e valores para ponto flutuante ainda na planilha outra opção é colocar valor zero para estes campos vez que não é utilizado na inspeção, bem como ajustar os espaços que as células do excel deixa quando da transformação do arquivo para (arquivo.sql). Nesse caso o arquivo pode ter até 1gb.
                            </textarea>
                      </div>


                      <form action="{{ route('compliance.importacao.bdf_fat_02') }}" method="POST" name="importform"
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
                            <a class="btn waves-effect waves-teal orange" href="{{ url('/compliance/importacoes/bdf_fat_02/export') }}" disabled>Export File
                            <i class="material-icons right">file_download</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
