jQuery( function() {
    jQuery("#select-binding").change(function(){

      var selected_product = jQuery(this).val();

      var url = window.location.href;
      var a = url.indexOf("?");
      var b =  url.substring(a);
      var c = url.replace(b,"");
      current_product_url = c;
      var redirect_url = current_product_url+'?selectedproduct='+selected_product;


      window.location.href = redirect_url;


    });

    function custom_template(obj){
        var data = jQuery(obj.element).data();
        var text = jQuery(obj.element).text();

        if(data && data['img_src'] && data['price']){
            img_src = data['img_src'];
            price_int  = parseInt(data['price']);
            price  = price_int.toFixed(2);
            template = jQuery("<div class=\"binding-option-parent\"><img src=\"" + img_src + "\" class=\"binding-option-img\"/><div class=\"binding-option-text\"><span class=\"binding-option-dropdown\">" + text + "</span><span class=\"binding-option-dropdown-price\">"+ " -$" + price + "</span></div></div>");
            return template;
        }
    }

    var options = {
        'templateSelection': custom_template,
        'templateResult': custom_template,
        'width': 'off'
   }
    jQuery('#select-binding').select2(options);
    jQuery('.select2-container--default .select2-selection--single').css({'height': '100%'});

    // jQuery("#select-binding").change(function(){

    //   var selected_product = jQuery(this).val();

    //   var url = window.location.href;
    //   var a = url.indexOf("?");
    //   var b =  url.substring(a);
    //   var c = url.replace(b,"");
    //   current_product_url = c;
    //   var redirect_url = current_product_url+'?binding='+selected_product;


    //   window.location.href = redirect_url;


    // });

    jQuery('a[href="#product_details"]').click(function(event) {
      event.preventDefault();
      jQuery(this).modal({
        fadeDuration: 1000,
        fadeDelay: 0.50
      });
    });


} );