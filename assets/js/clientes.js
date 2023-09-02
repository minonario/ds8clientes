/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


jQuery( function ( $ ) {

  $('[data-toggle="tooltip"]').tooltip();
  
  $(document).on('click','.cardClientes', function(event){
    event.stopPropagation();
    event.preventDefault();
    var $this = $(event.target).closest('.card');
    console.log('href:'+$this.data("cliente"));
    document.location.href = $this.data("cliente");
  });
  
  $(document).on('click','.cardEmision .btn-estadistica', function(event){
    console.log($(this).data('link'));
    location.replace($(this).data('link'));
  });
  
  var getUrlParameter = function getUrlParameter(sParam) {
      var sPageURL = window.location.search.substring(1),
          sURLVariables = sPageURL.split('&'),
          sParameterName,
          i;

      for (i = 0; i < sURLVariables.length; i++) {
          sParameterName = sURLVariables[i].split('=');

          if (sParameterName[0] === sParam) {
              return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
          }
      }
      return false;
  };
  
  var order_ = getUrlParameter('orderc');
  
  if (order_ == 'fecha'){
    $('[name="toggle"]').prop( "checked", true );
    console.log('checked');
  }
  
  
  
  $('[name="toggle"]').change(function() {
    console.log('order clients::'+$('.row-pagination').data('perpage'));
    var order;
    
    $('.postsContent').html('<div class="spinner-border" role="status"><span class="sr-only"> Cargando... </span> </div>');
    $('.postsContent').addClass('loadings');
    if ($(this).is(':checked')) {
       order = 'fecha';
    }else{
       order = 'display_name';
    };
    
    $.ajax({
        url: clientes.ajaxurl,
        type: "GET",
        data: {action: 'clientes_action',
          orderc: order,
          //perpage: $('.row-pagination').data('perpage'),
          security: clientes.security
        },  
        success: function (response) {
            console.log(response);
            $('.xpostsContent').replaceWith(response['data']['page']);
            if (response) {
                //console.log(response['data']);
            }
            $('.postsContent').removeClass('loadings');
        }
    });
    
  });
  
});
