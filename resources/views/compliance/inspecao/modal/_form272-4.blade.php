
                    @if(isset($cftvs)&&(!empty($cftvs)))
                    <table class="highlight">
                            <thead>
                                <tr>
                                    <th>Link</th>
                                    <th>Cameras Fixas</th>
                                    <th>Cameras Infra</th>
                                    <th>Dome</th>
                                    <th>Usuário</th>
                                    <th>Passwd</th>
                                    <th>Marca/Modelo</th>
                                    <th>Status/Conectividade</th>
                                    <th>Data/Status</th>
                                    <th>Data/Equipamento</th>
                                    <th>Hora/Equipamento</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($cftvs as $cftv)
                                <tr>
                                    <td>
                                        <a href="{{$cftv->link}}"    target="_blank"> DVR {{$cftv->unidade}} </a>
                                    </td>
                                    <td>{{ $cftv->cameras_fixa_cf }}</td>
                                    <td>{{ $cftv->cameras_infra_vermelho_cir }}</td>
                                    <td>{{ $cftv->dome }}</td>
                                    <td>{{ $cftv->user }}</td>
                                    <td>{{ $cftv->password }}</td>
                                    <td>{{ $cftv->marcamodelo }}</td>
                                    <td>{{ $cftv->statusconexao }}</td>
                                    <td>{{(isset($cftv->data_ultima_conexao) && $cftv->data_ultima_conexao == ''  ? '   ----------  ' : \Carbon\Carbon::parse($cftv->data_ultima_conexao)->format('d/m/Y'))}}</td>
                                    <td>{{(isset($cftv->data_no_equipamento) && $cftv->data_no_equipamento == ''  ? '   ----------  ' : \Carbon\Carbon::parse($cftv->data_no_equipamento)->format('d/m/Y'))}}</td>
                                    <td>{{ $cftv->hora_no_equipamento }}</td>
                                </tr>
                            @endforeach
                            @if($cftv->data_ultima_conexao > 0)
                             <tr>
                               Com base nas informações disponibilizadas, acesse o  Sistema CFTV e anote no Formulário principal a sua avaliação e as evidências. {{\Carbon\Carbon::parse($cftv->data_no_equipamento)->format('d/m/Y')}}. Click no botão FECHAR.
                             </tr>
                             <div id="aprimoramento">
                                    <span>
                                       Se CONFORME:<br/> Em inspeção online realizada na unidade, constatou-se que o sistema de imagens (CFTV) funcionava corretamente não havia obstrução das câmeras, tendo sido constatado acurácia das imagens e histórico de gravação de todas câmeras.

                                <br/> Outras dicas - Se NÃO CONFORME: <br/>
                                Não foi possível avaliar as imagens em 26/08/2020 dado que o sistema não conectou. por alta de instalação de um novo plugin. Não sendo possível instalar dado que este inspetor não é administrador do sistema.

                                OU

                                Em inspeção online realizada na unidade, constatou-se que o sistema de imagens (CFTV) funcionava corretamente não havia obstrução das câmeras, tendo sido constatado acurácia das imagens e histórico de gravação de todas câmeras.

                                OU

                                Não foi possível acessar o sistema por meio do endereço de IP. O Sistema falha na conexão.  Não sendo possível determinar sobre o funcionamento das câmeras.

                                OU

                                Não foi possível avaliar as imagens dado que o sistema apresentou falha de network.

                                Ou

                                Em análise às imagens do sistema CFTV) constatou-se que o mesmo não estava funcionando adequadamente E estava com câmeras obstruídas por xxxxxxxxxxxxxxxxxxxxxxxxxxx (caixas, estantes, armários etc.) E  mal posicionadas, conforme relatado a seguir:

                                Ex:
                                a)	As câmeras 03, 05, 06 e 08 com deficiência na acurácia e desfocada.
                                b)	Para as câmeras 03, 05, 06 e 08 verificou-se também que não havia registro de imagens para datas anteriores.
                                c)	Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                                    </span>
                             </div>
                             <div id="historico"></div>
                             <div id="historico1"></div>
                            @endif
                            </tbody>
                        </table>
                    @endif
                    <input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
                    <input type="hidden"  id="totalrisco" value="0.00">
                    <input type="hidden"  id="totalsobra" value="0.00">
