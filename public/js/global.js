var Main = {
    init:function()
    {
        //setup input label swapping globally
        $("input.replaceValue").each(function(){$(this).attr('initVal',$(this).val());});
        $("input.replaceValue").focus(function(){
            if ($(this).val() == $(this).attr('initVal'))
                $(this).val('')
        });
        $("input.replaceValue").blur(function(){
            if($(this).val() =='')
                $(this).val($(this).attr('initVal'));
        });

    }
};

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