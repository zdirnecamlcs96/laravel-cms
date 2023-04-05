$(window).scroll(function(){
    if ($(window).scrollTop() >= 50) {
        $('#fixed-top').addClass('fixed-header');
    }
    else {
        $('#fixed-top').removeClass('fixed-header');
    }
});

