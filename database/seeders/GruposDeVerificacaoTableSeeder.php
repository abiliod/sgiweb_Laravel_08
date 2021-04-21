<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GruposDeVerificacaoTableSeeder extends Seeder

{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gruposdeverificacao')->insert([
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'230','nomegrupo'=>'FINANCEIRO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'231','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'232','nomegrupo'=>'SEGURANÇA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'233','nomegrupo'=>'BENS MÓVEIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'234','nomegrupo'=>'INFRAESTRUTURA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'235','nomegrupo'=>'CONDIÇÕES DE ACEITAÇÃO, CLASSIFICAÇÃO E TARIFAÇÃO DE OBJETOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'236','nomegrupo'=>'CAIXA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'237','nomegrupo'=>'ENTREGA INTERNA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'238','nomegrupo'=>'ASPECTOS COMERCIAIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'239','nomegrupo'=>'CARGA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'240','nomegrupo'=>'DISTRIBUIÇÃO DOMICILIÁRIA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'241','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'242','nomegrupo'=>'SEGURANÇA NO TRABALHO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'212','nomegrupo'=>'FATURAMENTO E TARIFAÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'213','nomegrupo'=>'PROTEÇÃO DE RECEITAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'214','nomegrupo'=>'PRODUTOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'215','nomegrupo'=>'MÁQUINA DE FRANQUEAR'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'216','nomegrupo'=>'ESTOQUE DE ETIQUETAS E COMPROVANTES'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'217','nomegrupo'=>'ATENDIMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'218','nomegrupo'=>'CAIXA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'219','nomegrupo'=>'CONTRATOS COMERCIAIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'220','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'221','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'222','nomegrupo'=>'EXPEDIÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'223','nomegrupo'=>'ORGANIZAÇÃO DA UNIDADE'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'224','nomegrupo'=>'GERENCIAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'225','nomegrupo'=>'INFRAESTRUTURA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'226','nomegrupo'=>'AGF - GERENCIAL - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'227','nomegrupo'=>'AGF - ORGANIZAÇÃO - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'228','nomegrupo'=>'AGF - PROCESSOS - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'200','nomegrupo'=>'CARGA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'201','nomegrupo'=>'DISTRIBUIÇÃO DOMICILIÁRIA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'202','nomegrupo'=>'PROTEÇÃO DE RECEITAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'203','nomegrupo'=>'UNIDADES CENTRALIZADORAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'204','nomegrupo'=>'ENTREGA INTERNA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'205','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'206','nomegrupo'=>'SEGURANÇA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'207','nomegrupo'=>'BENS MÓVEIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'208','nomegrupo'=>'INFRAESTRUTURA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'209','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'210','nomegrupo'=>'SEGURANÇA NO TRABALHO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'211','nomegrupo'=>'TRATAMENTO (SUBCENTRALIZADORA)'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'200','nomegrupo'=>'CARGA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'201','nomegrupo'=>'DISTRIBUIÇÃO DOMICILIÁRIA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'202','nomegrupo'=>'PROTEÇÃO DE RECEITAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'203','nomegrupo'=>'UNIDADES CENTRALIZADORAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'204','nomegrupo'=>'ENTREGA INTERNA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'205','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'206','nomegrupo'=>'SEGURANÇA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'207','nomegrupo'=>'SEGURANÇA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'208','nomegrupo'=>'BENS MÓVEIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'209','nomegrupo'=>'INFRAESTRUTURA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'210','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'211','nomegrupo'=>'SEGURANÇA NO TRABALHO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'500','nomegrupo'=>'TRATAMENTO (SUBCENTRALIZADORA)'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'303','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'304','nomegrupo'=>'PROTEÇÃO DE RECEITAS (ENCOMENDAS)'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'305','nomegrupo'=>'TRANSPORTE E ACONDICIONAMENTO DA CARGA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'306','nomegrupo'=>'BENS MÓVEIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'307','nomegrupo'=>'INFRAESTRUTURA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'308','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'309','nomegrupo'=>'SEGURANÇA PATRIMONIAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'310','nomegrupo'=>'SEGURANÇA NO TRABALHO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'311','nomegrupo'=>'TRABALHOS PREPARATÓRIOS - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'312','nomegrupo'=>'DESCARREGAMENTO E CARREGAMENTO - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'313','nomegrupo'=>'PRÉ-ABERTURA, ABERTURA E PRÉ-TRIAGEM -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'314','nomegrupo'=>'TRATAMENTO DE MENSAGENS -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'315','nomegrupo'=>'TRATAMENTO MANUAL DE ENCOMENDAS E MALOTES -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'316','nomegrupo'=>'TRATAMENTO AUTOMATIZADO DE ENCOMENDAS E MALOTES -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'317','nomegrupo'=>'EXPEDIÇÃO -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'318','nomegrupo'=>'RECONDICIONAMENTO -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'319','nomegrupo'=>'OPERAÇÃO DE EQUIPAMENTOS DE SEGURANÇA POSTAL -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'320','nomegrupo'=>'GESTÃO DE UNITIZADORES -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'321','nomegrupo'=>'LISTA GERAL -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'322','nomegrupo'=>'PLANEJAMENTO E QUALIDADE -  PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'21','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'31','numeroGrupoVerificacao'=>'400','nomegrupo'=>'CONTROLE E CAPTAÇÃO GCCAP/CCCAP (MENSAGENS)'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'31','numeroGrupoVerificacao'=>'401','nomegrupo'=>'CONTROLE E CAPTAÇÃO GCCAP/CCCAP (ENCOMENDAS)'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'31','numeroGrupoVerificacao'=>'402','nomegrupo'=>'CONTROLE E CAPTAÇÃO GCCAP/CCCAP (MENSAGENS E ENCOMENDAS)'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'31','numeroGrupoVerificacao'=>'403','nomegrupo'=>'PROTEÇÃO DE RECEITAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'31','numeroGrupoVerificacao'=>'404','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'31','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'270','nomegrupo'=>'FINANCEIRO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'271','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'272','nomegrupo'=>'SEGURANÇA '],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'273','nomegrupo'=>'BENS MÓVEIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'274','nomegrupo'=>'CONDIÇÕES DE ACEITAÇÃO, CLASSIFICAÇÃO E TARIFAÇÃO DE OBJETOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'275','nomegrupo'=>'ENTREGA INTERNA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'276','nomegrupo'=>'CARGA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'277','nomegrupo'=>'DISTRIBUIÇÃO, EXPEDIÇÃO E CONFERÊNCIA DA CARGA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'278','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'1','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'331','nomegrupo'=>'DISTRIBUIÇÃO, EXPEDIÇÃO E CONFERÊNCIA DA CARGA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'332','nomegrupo'=>'PROTEÇÃO DE RECEITAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'333','nomegrupo'=>'ENTREGA INTERNA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'334','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'335','nomegrupo'=>'SEGURANÇA '],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'336','nomegrupo'=>'BENS MÓVEIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'337','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'14','numeroGrupoVerificacao'=>'500','nomegrupo'=>'500- OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'330','nomegrupo'=>'CARGA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'331','nomegrupo'=>'DISTRIBUIÇÃO, EXPEDIÇÃO E CONFERÊNCIA DA CARGA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'332','nomegrupo'=>'PROTEÇÃO DE RECEITAS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'333','nomegrupo'=>'ENTREGA INTERNA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'334','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'335','nomegrupo'=>'SEGURANÇA '],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'336','nomegrupo'=>'BENS MÓVEIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'337','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'16','numeroGrupoVerificacao'=>'500','nomegrupo'=>'500- OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'350','nomegrupo'=>'FATURAMENTO E TARIFAÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'351','nomegrupo'=>'ATENDIMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'352','nomegrupo'=>'CONTRATOS COMERCIAIS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'353','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'354','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'355','nomegrupo'=>'EXPEDIÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'356','nomegrupo'=>'AGF - PROCESSOS - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'12','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'281','nomegrupo'=>'FATURAMENTO E TARIFAÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'282','nomegrupo'=>'MÁQUINA DE FRANQUEAR'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'283','nomegrupo'=>'ATENDIMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'284','nomegrupo'=>'PRODUTOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'285','nomegrupo'=>'COMERCIAL '],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'286','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'287','nomegrupo'=>'EXPEDIÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'288','nomegrupo'=>'GERENCIAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'289','nomegrupo'=>'INFRAESTRUTURA'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'290','nomegrupo'=>'ORGANIZAÇÃO DA UNIDADE'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'291','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'292','nomegrupo'=>'ACC - GERENCIAL - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'293','nomegrupo'=>'ACC - ORGANIZAÇÃO - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'294','nomegrupo'=>'ACC - PROCESSOS - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Presencial','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'370','nomegrupo'=>'FATURAMENTO E TARIFAÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'371','nomegrupo'=>'COMERCIAL '],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'372','nomegrupo'=>'SEGURANÇA POSTAL'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'373','nomegrupo'=>'EXPEDIÇÃO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'374','nomegrupo'=>'GERENCIAMENTO'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'375','nomegrupo'=>'ORGANIZAÇÃO DA UNIDADE'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'376','nomegrupo'=>'RECURSOS HUMANOS'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'377','nomegrupo'=>'ACC - PROCESSOS - PPP'],
//            ['ciclo'=>'2021','tipoVerificacao'=>'Remoto','tipoUnidade_id'=>'3','numeroGrupoVerificacao'=>'500','nomegrupo'=>'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],

            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '230','nomegrupo'  =>  'FINANCEIRO '],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '231','nomegrupo'  =>  'SEGURANÇA POSTAL'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '232','nomegrupo'  =>  'SEGURANÇA '],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '233','nomegrupo'  =>  'BENS MÓVEIS'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '234','nomegrupo'  =>  'INFRAESTRUTURA'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '235','nomegrupo'  =>  'CONDIÇÕES DE ACEITAÇÃO, CLASSIFICAÇÃO E TARIFAÇÃO DE OBJETOS'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '236','nomegrupo'  =>  'CAIXA POSTAL'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '237','nomegrupo'  =>  'ENTREGA INTERNA'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '238','nomegrupo'  =>  'ASPECTOS COMERCIAIS'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '239','nomegrupo'  =>  'CARGA POSTAL'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '240','nomegrupo'  =>  'DISTRIBUIÇÃO, EXPEDIÇÃO E CONFERÊNCIA DA CARGA'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '241','nomegrupo'  =>  'RECURSOS HUMANOS'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '242','nomegrupo'  =>  'SEGURANÇA NO TRABALHO'],
            ['ciclo'  =>  '2021','tipoVerificacao'  =>  'Monitorada','tipoUnidade_id'  =>  '1','numeroGrupoVerificacao'  =>  '500','nomegrupo'  =>  'OUTRAS OPORTUNIDADES DE APRIMORAMENTO'],









        ]);
        $this->command->info('Grupos de Inspecao De Unidades importados com sucesso!');
    }
}
