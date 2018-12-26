$(function(){

        var heightForWindow = $(window).innerHeight();
        console.log(heightForWindow);
        var heightForHeader = $("#header").innerHeight();
        console.log(heightForHeader);
        var heightForNav = $("#nav").innerHeight();
        console.log(heightForNav);
        var heightForFooter = $("#footer").innerHeight();
        console.log(heightForFooter);

        var sum = parseInt(heightForWindow) - parseInt(heightForNav) - parseInt(heightForFooter);
        console.log(sum);

        $(".sliderImg").innerHeight(sum);

        $(window).resize(function(){
            var heightForWindow = $(window).innerHeight();
            console.log(heightForWindow);
            var heightForHeader = $("#header").innerHeight();
            console.log(heightForHeader);
            var heightForNav = $("#nav").innerHeight();
            console.log(heightForNav);
            var heightForFooter = $("#footer").innerHeight();
            console.log(heightForFooter);

            var sum = parseInt(heightForWindow) - parseInt(heightForHeader) - parseInt(heightForNav) - parseInt(heightForFooter);
            console.log(sum);

            $(".sliderImg").innerHeight(sum);
        })

    }
);

var swiper = new Swiper('.swiper-container', {
//        pagination: '.swiper-pagination',
//        paginationClickable: true,
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev',
    loop: true,
    autoplay: 2500,
    lazyLoading: true,
    autoplayDisableOnInteraction: false
});