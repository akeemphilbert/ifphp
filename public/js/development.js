var Support={
    init: function(){
        $('.supportAnswer').hide();
          $('a').click(
              function(event){                  
                    var id = $(this).attr('href');
                    $(id).toggle();
                    return false;
              }
         );          
    }
}