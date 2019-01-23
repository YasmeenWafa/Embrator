import slick from "slick-carousel";
let uparrow =$('.up-arrow');
let downarrow =$('.down-arrow');
// console.log(uparrow)
$(".years-slider").slick({
    dots: true,
    arrows: true,
    infinite: false,
    speed: 1500,
    slidesToShow:1,
    slidesToScroll:1,
    autoplay: true,
    autoplaySpeed:5000,
    vertical: true,
    verticalSwiping: true,
    prevArrow: "<a  class='slick-prev '></a>",
    nextArrow: "<a class='slick-next '></a>"
});
$('.slick-dots li').each(function(i){
  // console.log($('.year-slide .year-button')[i]);
  // console.log(i)
  // console.log($('.years .year .year-button'))

  // console.log($('.years .year .year-button')[i])
  let button = $('.years .year .year-button')[0];
  // var button = JSON.parse(JSON.stringify($('.years .year .year-button')[i]))
  // console.log(button)
  $(this).append(button);
});

$('.slick-prev').append(uparrow);
$('.slick-next').append(downarrow);
