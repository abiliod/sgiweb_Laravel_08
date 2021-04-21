function AtualizarTotalPontos()
{
    /*
    - converter os valores dos inputs da tea Itens de verificação
      para inteiro utilizando parseInt.
      Somar e Atualizar o campo TotalPontos na Tela do usuário
      -> o número 10 significa em qual base será convertido o número,no caso Decimal.
    */
    document.getElementById("totalPontos").value
       = parseInt(document.getElementById("impactoFinanceiro").value,10)
       + parseInt(document.getElementById("riscoFinanceiro").value,10)
       + parseInt(document.getElementById("descumprimentoLeisContratos").value,10)
       + parseInt(document.getElementById("descumprimentoNormaInterna").value,10)
       + parseInt(document.getElementById("riscoSegurancaIntegridade").value,10)
       + parseInt(document.getElementById("riscoImgInstitucional").value,10);
}

function ativaBtnFiltro()
{
    /**
     * Ativa o botão filtrar das paginas de crud de Tipo de unidades
     * e itens de verificação
    */

    let tipoUnidade_id = parseInt(document.getElementById("tipoUnidade_id").value,10);
    let nomegrupo = document.getElementById("nomegrupo").text;
    let tipoVerificacao = document.getElementById("tipoVerificacao").text;
    document.getElementById("btnFiltrar").disabled = true;

    if ((tipoUnidade_id  >= 1)&&(tipoVerificacao != "0"))
    {
        document.getElementById("btnFiltrar").disabled = false;
    } else if ((tipoUnidade_id  >= 0) && (nomegrupo !="Selecione um Grupo de Unidade")){
        document.getElementById("btnFiltrar").disabled = false;
    }else if(tipoVerificacao == "0")
    {
        document.getElementById("btnFiltrar").disabled = false;
    } else
    {
        document.getElementById("btnFiltrar").disabled = true;
    }
}

function mostrarDiv() {
    if(document.body.contains(document.getElementById('grupoVerificacao_id'))){
        let x = document.getElementById("grupoVerificacao_id").value;

        if (((x >= "15") && (x <= "32")) ||( (x >= "46") && (x <= "58")) || ((x >= "105") && (x <= "121"))) {
            document.getElementById("CampoOculto").style.display = "block";
        } else {
            document.getElementById("CampoOculto").style.display = "none";
        }
    }

}





function Mudarestado(el)
{
    let display = document.getElementById(el).style.display;

    if (display=='none')
    {
        document.getElementById(el).style.display= 'block';
    }else
    {
       document.getElementById(el).style.display= 'none';
    }

}

function ItemQuantificado(el)
{
    let valor = document.getElementById(el).value;
    let elemento = document.getElementById(el).name;

    if ((elemento=='itemQuantificado')&& (valor=='Sim'))
    {
        document.getElementById("quantificacao").style.display= 'block';
    }else
    {
        document.getElementById("quantificacao").style.display= 'none';
    }

}

function Reincidencia(el)
{
    let valor = document.getElementById(el).value;
    let elemento = document.getElementById(el).name;

    if ((elemento=='reincidencia')&& (valor=='Sim'))
    {
        document.getElementById("reincidencias").style.display= 'block';
    }else
    {
        document.getElementById("reincidencias").style.display= 'none';
    }

}

//////////////////////



function avalia(el) { // muda o estado do apontamento pelo click do usuário na opção Radio do modal
    let valor;
    let totalfalta = document.getElementById("totalfalta").value;
    let totalrisco = document.getElementById("totalrisco").value;
    let totalsobra = document.getElementById("totalsobra").value;

    let rads = document.getElementsByName(el);
        for(let i = 0; i < rads.length; i++) {
            if(rads[i].checked){
                valor = rads[i].value;
            }
         }
        if (valor=='Conforme') {
          //  alert(valor );
           document.getElementById("avaliacao1").checked = valor;
           conteudo = document.getElementById("aprimoramento").innerText;
           document.getElementById("oportunidadeAprimoramento").value = conteudo;
           document.getElementById("evidencia").value = '';

           document.getElementById("norma").style.display = 'block';
           document.getElementById("consequencias").value = '';
           document.getElementById("consequencias").style.display = 'none';
           document.getElementById("orientacao").value = '';
           document.getElementById("orientacao").style.display = 'none';
           document.getElementById("evidencias").value = '';
           document.getElementById("evidencias").style.display = 'none';
           document.getElementById("reincidencia").value = 'Não';
           document.getElementById("numeroGrupoReincidente").value = '0';
           document.getElementById("numeroItemReincidente").value = '0';
           document.getElementById("codVerificacaoAnterior").value = '';
           document.getElementById("reincidencia").value = 'Não';
           document.getElementById("reincidencia").style.display = 'none';
           document.getElementById("ereincidencia").style.display = 'none';
           document.getElementById("imagens").style.display = 'none';
           document.getElementById("quantificacao").style.display = 'none';
           document.getElementById("itemsQuantificados").style.display = 'none';

         //  alert(conteudo );
        }else if (valor=="Não Conforme") {

            document.getElementById("avaliacao2").checked = valor;
            conteudo = document.getElementById("aprimoramento").innerText;
            document.getElementById("oportunidadeAprimoramento").value = conteudo;

            // historico = document.getElementById("historico1").innerText;

            // if(document.getElementById("historico1").value!==''){
            //     historico = document.getElementById("historico").innerText;
            //     historico =  historico  +  '\n' + document.getElementById("historico1").innerText;
            // } else {
            //     historico = document.getElementById("historico").innerText;
            // }

            // if (document.getElementById("opcao").exists) {
            // }
            //     document.getElementById("opcao").innerText;
            // }
            historico = document.getElementById("historico").innerText;

            historico =  historico  +  '\n' + document.getElementById("historico1").innerText;
            //alert( historico );
            document.getElementById("evidencia").style.display = 'block';

            document.getElementById("evidencia").value = historico;

            document.getElementById("norma").style.display = 'block';
            document.getElementById("consequencias").style.display = 'block';
            document.getElementById("orientacao").style.display = 'block';
            document.getElementById("evidencias").style.display = 'block';
            document.getElementById("itemsQuantificados").style.display = 'block';
            document.getElementById("ereincidencia").style.display = 'block';
            document.getElementById("imagens").style.display = 'block';

                if ((totalfalta !=0) || (totalrisco !=0) || (totalsobra !=0)){

                    document.getElementById("itemQuantificado1").checked = true;
                    document.getElementById("quantificacao").style.display = 'block';
                    document.getElementById("valorFalta").value = totalfalta;
                    document.getElementById("valorRisco").value = totalrisco;
                    document.getElementById("valorSobra").value = totalsobra;
                }

            }else if (valor=="Não Verificado") {

                document.getElementById("avaliacao4").checked = valor;

                conteudo = document.getElementById("aprimoramento").innerText;
                document.getElementById("oportunidadeAprimoramento").value = conteudo;

                document.getElementById("norma").style.display = 'block';
                document.getElementById("consequencias").style.display = 'none';
                document.getElementById("orientacao").style.display = 'none';
                document.getElementById("evidencias").style.display = 'none';
                document.getElementById("ereincidencia").style.display = 'none';
                document.getElementById("imagens").style.display = 'none';
                document.getElementById("quantificacao").style.display = 'none';
                document.getElementById("itemsQuantificados").style.display = 'none';
            }else {

                document.getElementById("avaliacao3").checked = valor;
                conteudo = document.getElementById("aprimoramento").innerText;
          //  alert( conteudo );
                document.getElementById("oportunidadeAprimoramento").value = conteudo;
                document.getElementById("norma").style.display = 'block';
                document.getElementById("consequencias").style.display = 'none';
                document.getElementById("orientacao").style.display = 'none';
                document.getElementById("evidencias").style.display = 'none';
                document.getElementById("ereincidencia").style.display = 'none';
                document.getElementById("imagens").style.display = 'none';
                document.getElementById("quantificacao").style.display = 'none';
                document.getElementById("itemsQuantificados").style.display = 'none';
            }

          //  id="fecharModal"

          document.getElementById("oportunidadeAprimoramento").blur();
          document.getElementById("oportunidadeAprimoramento").focus();
          document.getElementById("modal1").style.display = 'none';
}



function mudarApontamento(el) { // muda o estado do apontamento pelo click do usuário na opção Radio
    let valor;
    let rads = document.getElementsByName(el);
        for(let i = 0; i < rads.length; i++) {
            if(rads[i].checked){
                valor = rads[i].value;
            }
         }
     //alert(valor );
        if (valor=='Conforme') {
          //  alert(valor );

           document.getElementById("norma").style.display = 'block';
           document.getElementById("consequencias").value = '';
           document.getElementById("consequencias").style.display = 'none';
           document.getElementById("orientacao").value = '';
           document.getElementById("orientacao").style.display = 'none';
           document.getElementById("evidencias").value = '';
           document.getElementById("evidencias").style.display = 'none';
           document.getElementById("reincidencia").value = 'Não';
           document.getElementById("numeroGrupoReincidente").value = '0';
           document.getElementById("numeroItemReincidente").value = '0';
           document.getElementById("codVerificacaoAnterior").value = '';
           document.getElementById("reincidencia").value = 'Não';
           document.getElementById("reincidencia").style.display = 'none';
           document.getElementById("ereincidencia").style.display = 'none';
           document.getElementById("imagens").style.display = 'none';
           document.getElementById("quantificacao").style.display = 'none';
           document.getElementById("itemsQuantificados").style.display = 'none';

           $(function(){
               var texto = $("#roteiroConforme").val();
               $("#oportunidadeAprimoramento").val(texto);
            });

        }else if (valor=="Não Conforme") {
          //    alert(valor );
                document.getElementById("norma").style.display = 'block';
                document.getElementById("consequencias").style.display = 'block';
                document.getElementById("orientacao").style.display = 'block';
                document.getElementById("evidencias").style.display = 'block';
                document.getElementById("itemsQuantificados").style.display = 'block';
                document.getElementById("ereincidencia").style.display = 'block';
               // document.getElementById("quantificacao").style.display = 'block';

             //   document.getElementsByName("itemQuantificado").enabled = 'false';
          //  quantificacao
                $(function(){
                    var texto = $("#roteiroNaoConforme").val(); // + $("#norma").val();
                    $("#oportunidadeAprimoramento").val(texto);
                });

            }else if (valor=="Não Verificado") {

                document.getElementById("norma").style.display = 'block';
                document.getElementById("consequencias").style.display = 'none';
                document.getElementById("orientacao").style.display = 'none';
                document.getElementById("evidencias").style.display = 'none';
                document.getElementById("ereincidencia").style.display = 'none';
                document.getElementById("imagens").style.display = 'none';
                document.getElementById("quantificacao").style.display = 'none';
                document.getElementById("itemsQuantificados").style.display = 'none';


                $(function(){
                    var texto = $("#roteiroNaoVerificado").val(); // + $("#norma").val();
                    $("#oportunidadeAprimoramento").val(texto);
                });
            }else {
                document.getElementById("norma").style.display = 'block';
                document.getElementById("consequencias").style.display = 'none';
                document.getElementById("orientacao").style.display = 'none';
                document.getElementById("evidencias").style.display = 'none';
             //   document.getElementById("oportunidadeAprimoramento").value ="";
                document.getElementById("ereincidencia").style.display = 'none';
                document.getElementById("imagens").style.display = 'none';
                document.getElementById("quantificacao").style.display = 'none';
                document.getElementById("itemsQuantificados").style.display = 'none';

            }
}

$(function() {
    $('#oportunidadeAprimoramento').on('keyup paste', function() {
    var $el = $(this),
        offset = $el.innerHeight() - $el.height();

    if ($el.innerHeight < this.scrollHeight) {
        //Grow the field if scroll height is smaller
        $el.height(this.scrollHeight - offset);
    } else {
        //Shrink the field and then re-set it to the scroll height in case it needs to shrink
        $el.height(1);
        $el.height(this.scrollHeight - offset);
    }
    });
});

  function consideraEvidencias(el) { // muda o estado do radio automáticamente ao considerar a evidência
    //alert(valor);
      let valor = document.getElementById(el).checked;
      let totalfalta = document.getElementById("totalfalta").value;
      let totalrisco = document.getElementById("totalrisco").value;
      let totalsobra = document.getElementById("totalsobra").value;

      if(valor) {

            document.getElementById("avaliacao2").checked = valor;
            conteudo = document.getElementById("modalteste").innerText;
            document.getElementById("evidencias").style.display = 'block';
            document.getElementById("orientacao").style.display = 'block';
            document.getElementById("consequencias").style.display = 'block';
            document.getElementById("orientacao").style.display = 'block';
            document.getElementById("evidencias").style.display = 'block';
            document.getElementById("evidencia").value = conteudo;
            document.getElementById("itemsQuantificados").style.display = 'block';
            document.getElementById("ereincidencia").style.display = 'block';
            document.getElementById("imagens").style.display = 'block';

            $(function(){
                var texto = $("#roteiroNaoConforme").val(); // + $("#norma").val();
                $("#oportunidadeAprimoramento").val(texto);
            });
            if ((totalfalta !=0) || (totalrisco !=0) || (totalsobra !=0)){

                document.getElementById("itemQuantificado1").checked = true;
                document.getElementById("quantificacao").style.display = 'block';
                document.getElementById("valorFalta").value = totalfalta;
                document.getElementById("valorRisco").value = totalrisco;
                document.getElementById("valorSobra").value = totalsobra;
            }

      }else{

            document.getElementById("evidencia").value ="";
            document.getElementById("evidencias").style.display = 'none';
            document.getElementById("orientacao").style.display = 'none';
            document.getElementById("numeroGrupoReincidente").value = '0';
            document.getElementById("numeroItemReincidente").value = '0';
            document.getElementById("codVerificacaoAnterior").value = '';
            document.getElementById("reincidencia").value = 'Não';
            document.getElementById("reincidencia").style.display = 'none';
            document.getElementById("ereincidencia").style.display = 'none';
            document.getElementById("imagens").style.display = 'none';
            document.getElementById("consequencias").style.display = 'none';
            document.getElementById("valorRisco").value = '0.00';
            document.getElementById("valorFalta").value = '0.00';
            document.getElementById("valorSobra").value = '0.00';
            document.getElementById("itemQuantificado2").checked = true;
            document.getElementById("quantificacao").style.display = 'none';
            document.getElementById("itemsQuantificados").style.display = 'none';

            $(function(){
                var texto = $("#roteiroConforme").val();
                $("#oportunidadeAprimoramento").val(texto);
            });

            document.getElementById("avaliacao1").checked = true;
      }

  }



 $("#imagem").change(function(){

    $('#imgPreview').html("");

    var total_file=document.getElementById("imagem").files.length;

    for(var i=0;i<total_file;i++)

    {

     $('#imgPreview').append("<img src='"+URL.createObjectURL(event.target.files[i])+"'>");

    }

 });

 $(document).ready(function(){
    $('#imagem').on('change', function(){ //on file input change
        if (window.File && window.FileReader && window.FileList && window.Blob)
       //if (window.File; window.FileReader; window.FileList; window.Blob) //check File API supported browser
       {

           var data = $(this)[0].files; //this file data

           $.each(data, function(index, file){ //loop though each file
               if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                   var fRead = new FileReader(); //new filereader
                   fRead.onload = (function(file){ //trigger function on successful read
                   return function(e) {
                       var img = $('<img width=120/>').addClass('thumb').attr('src', e.target.result); //create image element
                       $('#thumb-output').append(img); //append image to output element
                   };
                   })(file);
                   fRead.readAsDataURL(file); //URL representing the file's data.
               }
           });

       }else{
           alert("Your browser doesn't support File API!"); //if File API is absent
       }
    });
   });


/****
 *
 *
 * cep  **/

$(document).ready(function() {

    function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        $("#rua").val("");
        $("#bairro").val("");
        $("#cidade").val("");
        $("#uf").val("");
        $("#ibge").val("");
    }

    //Quando o campo cep perde o foco.
    $("#cep").blur(function() {

        //Nova variável "cep" somente com dígitos.
        var cep = $(this).val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                $("#rua").val("...");
                $("#bairro").val("...");
                $("#cidade").val("...");
                $("#uf").val("...");
                $("#ibge").val("...");

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#rua").val(dados.logradouro);
                        $("#bairro").val(dados.bairro);
                        $("#cidade").val(dados.localidade);
                        $("#uf").val(dados.uf);
                        $("#ibge").val(dados.ibge);
                    } //end if.
                    else {
                        //CEP pesquisado não foi encontrado.
                        limpa_formulário_cep();
                        alert("CEP não encontrado.");
                    }
                });
            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    });
});

//**apenas numeros no input */
function onlynumber(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    //var regex = /^[0-9.,]+$/;
    var regex = /^[0-9.]+$/;
    if( !regex.test(key) ) {
       theEvent.returnValue = false;
       if(theEvent.preventDefault) theEvent.preventDefault();
    }
 }

//   mask input
 jQuery(function($){
    $("#cpf").mask("999.999.999-99");
    $("#rg").mask("99.999.999-9");
    $("#tel").mask("(999) 99999-9999");
    $("#dt").mask("99/99/9999");
    $("#pass").mask("*******");
    $("#lt").mask("aaaaaaaaaaaaa");

    $("#money2dig").mask("9999.99");
    $("#money4dig").mask("9999.9999");
    $("#money6dig").mask("9.999999");

});
