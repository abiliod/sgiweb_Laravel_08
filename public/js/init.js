
$( document ).ready(function(){
//	M.AutoInit();
	$(".button-collapse").sideNav();
	$(".dropdown-button").dropdown();
    $(".slider").slider({full_width:true});
    $("select").material_select('destroy');
    $('.modal').modal();
    $("select").material_select();
    Materialize.updateTextFields();
    $('#textarea1').val('New Text');
    $('#textarea1').trigger('autoresize');
  




	$('.datepicker').pickadate({
		selectMonths: true, // Creates a dropdown to control month
		selectYears: 15, // Creates a dropdown of 15 years to control year,
		today: 'Today',
		clear: 'Clear',
		close: 'Ok',
		closeOnSelect: false, // Close upon selecting a date,
	//	container: undefined, // ex. 'body' will append picker to body
      });

})

