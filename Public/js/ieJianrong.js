$(function () {
    if($('.inputGGTs').val()!==''){
        $('.inputGGTs').siblings('.inputGGTx').css('display','none');
    }

    $('.inputGGTs').focus(function () {
        $(this).siblings('.inputGGTx').css('display','none');
    });
    $('.inputGGTs').blur(function () {
        console.log($(this).val());
        if($(this).val()==''){
            $(this).siblings('.inputGGTx').css('display','inline-block');
        }
    });

    $('.timeGGTs').focus(function () {
        $(this).siblings('.timeGGTx').css('display','none');
    });
});