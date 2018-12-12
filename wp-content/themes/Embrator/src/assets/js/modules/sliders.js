import slick from "slick-carousel";
// import { TweenLite, TimelineLite } from "gsap";
import "gsap";
// import _ from "underscore";
// $(".home__hero").slick({
//   infinite: false,
//   autoplay: false,
//   arrows: false,
//   lazyLoad: "ondemand",

//   // initialSlide: 1,
//   useTransform: false, // Prevent Flickering
//   vertical: true,
//   verticalSwiping: true,
//   dots: true,
//   // fade:true,
//   slidesToShow: 1,
//   customPaging: function(slider, i) {
//     if (i == 0) {
//       return `<a class="title__dots" href="#">
//       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="512px" height="512px">
//       <g>
//         <g>
//           <path class="svgPath" d="M296,116.049c-5.52,0-10,4.48-10,10c0,5.52,4.48,10,10,10c5.52,0,10-4.48,10-10C306,120.529,301.52,116.049,296,116.049z" fill="#FFFFFF"/>
//         </g>
//       </g>
//       <g>
//         <g>
//           <path class="svgPath" d="M502,76.049H10c-5.523,0-10,4.477-10,10v140c0,0.374,0.024,0.742,0.064,1.105c0.161,1.444,0.594,2.567,1.139,3.654    C10.23,245.349,54.63,348.869,185.15,430.889c5.111,3.211,6.804,5.062,10.85,5.062c0.023,0,0.046-0.003,0.07-0.003h119.86    c0.023,0,0.046,0.003,0.07,0.003c3.606,0,5.632-1.679,7.918-3.112c74.245-46.512,144.595-117.457,187.026-202.318    c0.623-1.243,1.056-2.871,1.056-4.472v-140C512,80.526,507.523,76.049,502,76.049z M26.881,236.712    c63.47,4.917,109.4,38.147,136.642,99.011c9.557,21.354,16.399,45.377,20.118,70.355    C102.579,351.96,54.454,286.941,26.881,236.712z M20.001,216.258v-40.209h186.062c0.054,4.635,0.136,9.109,0.218,13.49    c0.721,38.904,1.197,64.605-20.016,99.965l-12.789,21.32C148.936,266.056,103.451,219.697,20.001,216.258z M322.349,433.771    c0.077-0.062,0.146-0.119,0.151-0.123C322.474,433.67,322.414,433.718,322.349,433.771z M306.924,416.049H205.076    c-3.16-29.493-10.426-58.174-21.208-83.672l19.547-32.583c24.166-40.282,23.59-71.324,22.862-110.625    c-0.079-4.264-0.156-8.73-0.21-13.22h59.865c-0.054,4.489-0.131,8.955-0.21,13.22c-0.727,39.3-1.303,70.342,22.863,110.625    l19.561,32.602C317.356,357.891,310.085,386.566,306.924,416.049z M328.358,406.097c3.721-24.978,10.57-49.003,20.14-70.365    c27.229-60.863,73.145-94.095,136.6-99.019C447.53,304.942,390.066,364.22,328.358,406.097z M492,216.258    c-83.454,3.44-128.931,49.81-153.462,94.583l-12.802-21.337c-21.213-35.36-20.737-61.062-20.016-99.965    c0.081-4.382,0.163-8.856,0.218-13.49H492V216.258z M492,156.049H20v-60h472V156.049z" fill="#FFFFFF"/>
//         </g>
//       </g>
//       <g>
//         <g>
//           <path class="svgPath" d="M256,116.049h-40c-5.523,0-10,4.477-10,10c0,5.523,4.477,10,10,10h40c5.523,0,10-4.477,10-10    C266,120.526,261.523,116.049,256,116.049z" fill="#FFFFFF"/>
//         </g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       <g>
//       </g>
//       </svg>
//       </a>`;
//     } else if (i == 1) {
//       return `<a class="title__dots" href="#">
//         <svg
//           xmlns="http://www.w3.org/2000/svg"
//           xmlns:xlink="http://www.w3.org/1999/xlink"
//           version="1.1"
//           id="Layer_1"
//           x="0px"
//           y="0px"
//           viewBox="0 0 512 512"
//           style="enable-background:new 0 0 512 512;"
//           xml:space="preserve"
//           width="512px"
//           height="512px"
//         >
//           <g>
//             <g>
//               <path
//                 class="svgPath" d="M362.672,42.66h-53.328c0,29.414-23.938,53.336-53.344,53.336s-53.328-23.922-53.328-53.336h-53.336L0,127.996    l42.672,117.336l64-21.336V469.34h298.672V223.996l64,21.336L512,127.996L362.672,42.66z M456.375,218.527l-44.297-14.766    L384,194.395v29.602v224H128v-224v-29.602l-28.078,9.367l-44.297,14.766l-29.43-80.93L155,63.996h29.43    c9.18,30.844,37.75,53.336,71.57,53.336c33.828,0,62.391-22.492,71.562-53.336H357l128.812,73.602L456.375,218.527z"
//                 fill="#FFFFFF"
//               />
//             </g>
//           </g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//           <g></g>
//         </svg>
//       </a>`;
//     } else if (i == 2) {
//       return `<a class="title__dots" href="#">
//         <svg height="480pt" viewBox="-120 0 480 480.00004" width="480pt" xmlns="http://www.w3.org/2000/svg"><path d="m136 40h-128v432h88l24-312 24 312h88v-432zm0 0" fill="transparent"/><path class="svgPath" d="m232 0h-224c-4.417969 0-8 3.582031-8 8v464c0 4.417969 3.582031 8 8 8h88c4.1875.011719 7.675781-3.207031 8-7.382812l16-208.3125 16 208.3125c.324219 4.175781 3.8125 7.394531 8 7.382812h88c4.417969 0 8-3.582031 8-8v-464c0-4.417969-3.582031-8-8-8zm-216 16h208v16h-80v-8h-16v8h-16v-8h-16v8h-80zm135.40625 448-23.40625-304.617188c-.324219-4.164062-3.796875-7.378906-7.976562-7.378906-4.175782 0-7.652344 3.214844-7.976563 7.378906l-23.453125 304.617188h-72.59375v-416h80v56h16v-56h16v56h16v-56h80v416zm0 0" fill="white"/></svg>
//       </a>`;
//     }
//   }
// });
// function throttle(fn, wait) {
//   var time = Date.now();
//   return function() {
//     if (time + wait - Date.now() < 0) {
//       fn();
//       time = Date.now();
//     }
//   };
// }

// // function callback (e) {
// //   // e.preventDefault();
// //   if (e.originalEvent.deltaY < 0) {
// //     $(this).slick('slickNext');
// //   } else {
// //     $(this).slick('slickPrev');
// //   }
// // };
// // $(window).scroll(function(e){
// // });

// // $(window).on("wheel mousewheel", throttle((function(e){
// //   if(e.originalEvent.deltaY > 0) {
// //       e.preventDefault();
// //       $('.home__hero').slick("slickNext");
// //       return;
// //   } else if (e.originalEvent.wheelDeltaY < 0) {
// //       e.preventDefault();
// //       $('.home__hero').slick("slickPrev");
// //       return;
// //   }
// // }),1000));
// var sliding = false;
// $(".home__hero").on(
//   "wheel",
//   function (e) {

//     if ($(this).slick('slickCurrentSlide')!==2 || sliding ) {
//       e.preventDefault();
//     } else {
//       return
//     }
//     if(!sliding){
//       sliding=true;
//       if (window.event.deltaY < 0) {
//         $(this).slick("slickPrev");
//         // console.log($(this));
//       } else {
//         $(this).slick("slickNext");
//       }
//       setTimeout(() => {
//         sliding=false;
//       }, 1000);
//     }
// }
// );

// // $(".home__hero").on("wheel", function() {
// //   console.log(window.event);
// //   window.event.preventDefault();
// //   if (window.event.deltaY < 0) {
// //     console.log($(this));

// //     $(this).slick("slickNext");
// //   } else {
// //     $(this).slick("slickPrev");
// //   }
// // });

// // $(function() {
// //   $(window).on('scroll', '#ver', function(){
// //     alert("scroolled");
// //     console.log($(this).scrollTop());
// //     // e.preventDefault();

// //     // console.log(e.originalEvent.deltaY);
// //     // console.log(e.originalEvent.deltaX);
// //     // console.log(e.originalEvent.deltaZ);

// //     // if (e.originalEvent.deltaY < 0) {
// //     //   // console.log('thisiss');

// //     //   // console.log($(this).slick('slickNext'));

// //     //   $(this).slick('slickNext');
// //     //   // console.log('ntext');

// //     // } else {
// //     //   // console.log('prev');
// //     //   $(this).slick('slickPrev');
// //     // }
// //   });
// // });

// Delete This --------- from here---------------------------------------------------------->
// var $imageUrlPrevious, $imageUrlNext;
// $(".section__collectionsSlider").on("init", function(slick) {
//   // console.log(event, nextSlide, currentSlide, slick);
//   // console.log(slick.$slides);
//   setTimeout(currentSlide => {
//     // currentSlide?console.log(currentSlide):console.log('mama 2afla belmofta7');
//     var $slick = $(".section__collectionsSlider").slick("getSlick");
//     var $currentSlide = $(".section__collectionsSlider").slick(
//       "slickCurrentSlide"
//     );
//     var nextSlideIndex, previousSlideIndex;

//     if ($currentSlide + 1 > $slick.$slides.length) {
//       nextSlideIndex = 0;
//     } else {
//       nextSlideIndex = $currentSlide + 1;
//     }

//     if ($currentSlide - 1 < 0) {
//       previousSlideIndex = $slick.$slides.length - 1;
//     } else {
//       previousSlideIndex = $currentSlide - 1;
//     }

//     // The Issue is HERE

//     // var arr = [].slice.call($slick.$slides);

//     var backgroundImageCurrent = $($slick.$slides[$currentSlide]);
//     var backgroundImagePrevious = $($slick.$slides[previousSlideIndex]);
//     var backgroundImageNext = $($slick.$slides[nextSlideIndex]);

//     console.log(previousSlideIndex, nextSlideIndex);

//     $imageUrlPrevious = $(backgroundImagePrevious[0].innerHTML)
//       .find(".collectionsSlider__image")
//       .css("background-image");
//     $imageUrlNext = $(backgroundImageNext[0].innerHTML)
//       .find(".collectionsSlider__image")
//       .css("background-image");
//     // console.log(arr);
//     console.log($imageUrlPrevious, $imageUrlNext);
//     // backgroundImage = $(backgroundImage).find('.collectionSlider__image');
//     // backgroundImageUrl = backgroundImage.css('background-image').replace('url(','').replace(')','').replace(/\"/gi, "");
//     // console.log();

//     // console.log(backgroundImageUrl);

//     // console.log(slick.$slides[currentSlide]);
//   }, 0);
// });
//undefined
// console.log(
//   $imageUrlPrevious ? $imageUrlPrevious : "lessa ya 3asal",
//   $imageUrlNext ? $imageUrlNext : "lessa ya Basal"
// );
// -------------------------------- till here ---------------------------------------------------------->

$(".section__collectionsSlider").slick({
  infinite: true,
  autoplay: false,
  arrows: true,
  prevArrow: `<button type='button' class='slick-prev'><svg width="24px"style="background-image: url()" height="40px" viewBox="0 0 24 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <!-- Generator: Sketch 51.1 (57501) - http://www.bohemiancoding.com/sketch -->
        <title>Shape</title>
        <desc>Created with Sketch.</desc>
        <defs></defs>
        <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g id="arrow-left-1" fill="white" fill-rule="nonzero">
                <polygon id="Shape" transform="translate(12.000000, 20.000000) scale(-1, 1) translate(-12.000000, -20.000000) " points="4.54054054 0 0 4.66666667 14.9189189 20 0 35.3333333 4.54054054 40 24 20"></polygon>
            </g>
        </g>
    </svg></button>`,
  nextArrow: `<button type='button' class='slick-next'><svg width="24px" height="40px" viewBox="0 0 24 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <!-- Generator: Sketch 51.1 (57501) - http://www.bohemiancoding.com/sketch -->
        <title>Shape</title>
        <desc>Created with Sketch.</desc>
        <defs></defs>
        <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g id="arrow-left-1" fill="white" fill-rule="nonzero">
                <polygon id="Shape" transform="translate(12.000000, 20.000000) scale(-1, 1) translate(-12.000000, -20.000000) " points="4.54054054 0 0 4.66666667 14.9189189 20 0 35.3333333 4.54054054 40 24 20"></polygon>
            </g>
        </g>
    </svg></button>`,
  dots: false,
  slidesToShow: 1
});

// ----------------------------------Hero Slider ----------------------------------------------------

var $activeSlide = $(".active"),
  $homeSlide = $(".bannerSlider-figure"),
  $slideNavPrev = $(".slideNavPrev"),
  $slideNavNext = $(".slideNavNext"),
  $slideNavPrevA = $(".slideNavPrev a"),
  $slideNavNextA = $(".slideNavNext a"),
  $hero = $(".home__hero");
// ------------------------------------------
// Init function that run on page load
function init() {
  // Hide all slides apart from the active one
  TweenLite.set($homeSlide.not($activeSlide), { autoAlpha: 0 });

  // Disable arrow down on page load
  TweenLite.set($slideNavPrev, { autoAlpha: 0.2 });
}

// Run Init function
init();
// ------------------------------------------
// Go to next slide - pass 2 parameters - slideOut and slideIn
function goToNextSlide(slideOut, slideIn) {
  // console.log(slideOut);
  // console.log(slideIn);
  // console.log('next ya fag');

  var tl = new TimelineLite(),
    slideOutProduct = slideOut.find(".bannerSlider-product"),
    slideOutTitleTop = slideOut.find(".bannerSlider__title__top"),
    slideOutTitleCenter = slideOut.find(".bannerSlider__title__center"),
    slideInProduct = slideIn.find(".bannerSlider-product"),
    slideInTitleTop = slideIn.find(".bannerSlider__title__top"),
    slideInTitleCenter = slideIn.find(".bannerSlider__title__center"),
    index = slideIn.index(),
    size = $(".top .bannerSlider-figure").length;

  if (slideIn.length !== 0) {
    // go to the next slide timeline
    tl
      // move the new slide (the one about to enter viewport) out of the viewport and add class active
      .set(slideIn, { y: "100%", autoAlpha: 1, className: "+=active" })
      // remove class active from the currently active slide (slideOut)
      .set(slideOut, { className: "-=active" })
      // animate H1 and p of the active slide up and fade them out
      .to(
        [slideOutProduct, slideOutTitleTop, slideOutTitleCenter],
        0.3,
        { y: "-=15px", autoAlpha: 0, ease: Power3.easeInOut },
        0
      )
      // animate active slide up (out of the viewport)
      .to(slideOut, 0.5, { y: "-100%", ease: Power3.easeInOut }, 0)
      // animate new slide up (from out of the viewport)
      .to(slideIn, 0.5, { y: "-=100%", ease: Power3.easeInOut }, 0)
      // animate H1 and P of the new slide up and fade them in
      .fromTo(
        [slideInProduct, slideInTitleTop, slideInTitleCenter],
        0.3,
        { y: "+=20px", autoAlpha: 0 },
        { autoAlpha: 1, y: 0, ease: Power1.easeInOut },
        0.3
      );
  }

  // Fade out arrow up and fade in arrow down

  // Fade in arrow down
  TweenLite.set($slideNavPrev, { autoAlpha: 1 });

  // Fade out arrow up on last slide
  if (index === size) {
    TweenLite.to($slideNavNext, 0.3, { autoAlpha: 0.2, ease: Linear.easeNone });
  }
}

//---------------------------------------------------------------------------------
// Go to previous slide - pass 2 parameters - slideOut and slideIn
function goToPreviousSlide(slideOut, slideIn) {
  // console.log('prev ya fag');
  var tl = new TimelineLite(),
    slideOutProduct = slideOut.find(".bannerSlider-product"),
    slideOutTitleTop = slideOut.find(".bannerSlider__title__top"),
    slideOutTitleCenter = slideOut.find(".bannerSlider__title__center"),
    slideInProduct = slideIn.find(".bannerSlider-product"),
    slideInTitleTop = slideIn.find(".bannerSlider__title__top"),
    slideInTitleCenter = slideIn.find(".bannerSlider__title__center"),
    index = slideIn.index(),
    size = $(".top .bannerSlider-figure").length;

  if (slideIn.length !== 0) {
    // go to the previous slide timeline
    tl
      // move the new slide (the one about to enter viewport) out of the viewport (to the top)
      .set(slideIn, { y: "-100%", autoAlpha: 1, className: "+=active" })
      // remove class active from the currently active slide (slideOut)
      .set(slideOut, { className: "-=active" })
      // animate H1 and p of the active slide down and fade them out
      .to(
        [slideOutProduct, slideOutTitleTop, slideOutTitleCenter],
        0.3,
        { y: "+=15px", autoAlpha: 0, ease: Power3.easeInOut },
        0
      )
      // animate active slide down (out of the viewport)
      .to(slideOut, 0.5, { y: "100%", ease: Power3.easeInOut }, 0)
      // animate new slide down (from out of the viewport)
      .to(slideIn, 0.5, { y: "+=100%", ease: Power3.easeInOut }, "-=0.5")
      // animate H1 and P of the new slide down and fade them in
      .fromTo(
        [slideInProduct, slideInTitleTop, slideInTitleCenter],
        0.3,
        { y: "-=20px", autoAlpha: 0 },
        { autoAlpha: 1, y: 0, ease: Power1.easeInOut },
        0.3
      );
  }

  // Fade in arrow up
  TweenLite.set($slideNavNext, { autoAlpha: 1 });

  // Fade out arrow down on first slide
  if (index === 1) {
    TweenLite.to($slideNavPrev, 0.3, { autoAlpha: 0.2, ease: Linear.easeNone });
  }
}

// Navigation click - go to the Next Slide
$slideNavNext.click(function(e) {
  e.preventDefault();

  var slideOut = $(".bannerSlider-figure.active"),
    slideIn = $(".bannerSlider-figure.active").next(".bannerSlider-figure");
  goToNextSlide(slideOut, slideIn);
});

// Navigation click - go to the Previous Slide
$slideNavPrev.click(function(e) {
  e.preventDefault();

  var slideOut = $(".bannerSlider-figure.active"),
    slideIn = $(".bannerSlider-figure.active").prev(".bannerSlider-figure");

  goToPreviousSlide(slideOut, slideIn);
});

// $(".home__hero").on("wheel", function(e) {
//   if ($(this).slick("slickCurrentSlide") !== 2 || sliding) {
//     e.preventDefault();
//   } else {
//     return;
//   }
//   var sliding = false;
//   if (!sliding) {
//     sliding = true;
//     if (window.event.deltaY < 0) {
//       var slideOut = $(".bannerSlider-figure.active"),
//         slideIn = $(".bannerSlider-figure.active").prev(".bannerSlider-figure");

//       goToPreviousSlide(slideOut, slideIn);
//       // console.log($(this));
//     } else {
//       var slideOut = $(".bannerSlider-figure.active"),
//         slideIn = $(".bannerSlider-figure.active").next(".bannerSlider-figure");
//       goToNextSlide(slideOut, slideIn);
//     }
//     setTimeout(() => {
//       sliding = false;
//     }, 1000);
//   }
// });

// // $(window).on("wheel mousewheel", throttle((function(e){
// //   if(e.originalEvent.deltaY > 0) {
// //       e.preventDefault();
// //       $('.home__hero').slick("slickNext");
// //       return;
// //   } else if (e.originalEvent.wheelDeltaY < 0) {
// //       e.preventDefault();
// //       $('.home__hero').slick("slickPrev");
// //       return;
// //   }
// // }),1000));
var h = $("#ver").outerHeight();
// console.log("h = " + h);

var last_known_scroll_position = 0,
  ticking = false,
  lastScrollPosition = 0;

// function onScrollSliderChange(scrollPosition) {
//   // console.log("ticking =" + ticking);
//   if (scrollPosition > lastScrollPosition) {
//     var slideOut = $(".bannerSlider-figure.active"),
//       slideIn = $(".bannerSlider-figure.active").next(".bannerSlider-figure");
//     // console.log(slideOut);
//     goToNextSlide(slideOut, slideIn);
//     // console.log("go to next");
//   } else {
//     var slideOut = $(".bannerSlider-figure.active"),
//       slideIn = $(".bannerSlider-figure.active").prev(".bannerSlider-figure");
//     goToPreviousSlide(slideOut, slideIn);
//     // console.log("prev ya danaya ");
//   }
//   lastScrollPosition = scrollPosition;
// }

// function onScroll(e) {
//   last_known_scroll_position = window.scrollY;
//   //   When Scrolling goes byond the container height make continer relative not fixed position
//   // console.log(last_known_scroll_position);

//   if (last_known_scroll_position < h) {
//     $("#ver").css("position", "fixed");
//     // $("body").addClass("preventScroll");
//     // e.preventDefault();
//     onScrollSliderChange(last_known_scroll_position);
//     // return;
//   } else {
//     $("#ver").css("position", "relative");
//     // $("body").removeClass("preventScroll");
//   }
// }

// var throttled = _.throttle(onScroll, 1000);
// $(window).scroll(throttled);

// Will Bind the scroll event to a function

// window.addEventListener(
//   "scroll",
//   throttled
//   //   if (!ticking) {
//   //     window.requestAnimationFrame(function() {
//   //       onScrollSliderChange(last_known_scroll_position);
//   //       ticking = false;
//   //     });
//   //     ticking = true;
//   //   }
//   // setInterval(onScrollSliderChange(last_known_scroll_position), 500);
// );
let sliding = false;
$(document).on("wheel mousewheel", function(event) {
  if (sliding) return;
  let deltaY = event.originalEvent.deltaY;
  let last_known_scroll_position = window.scrollY;

  // console.log(h);

  if (last_known_scroll_position < h ) {  //Needs Improvment 
    console.log('last_known_scroll_position ='+last_known_scroll_position );
    
    $("#ver").css("position", "fixed");
    $("body").addClass("preventScroll");
    if (Math.abs(deltaY) > 50) {
      sliding = true;
      console.log("oh yrah");

      if (deltaY > 0) {
        console.log("up");
        if ($(".lastSlide").hasClass("active")) {
          $("#ver").css("position", "relative");
          $("body").removeClass("preventScroll");
        }
        var slideOut = $(".bannerSlider-figure.active"),
          slideIn = $(".bannerSlider-figure.active").next(
            ".bannerSlider-figure"
          );
        // console.log(slideOut);
        goToNextSlide(slideOut, slideIn);
      }

      if (deltaY < 0) {
        console.log("down");
        
        var slideOut = $(".bannerSlider-figure.active"),
          slideIn = $(".bannerSlider-figure.active").prev(
            ".bannerSlider-figure"
          );
        goToPreviousSlide(slideOut, slideIn);
      }

      setTimeout(function() {
        sliding = false;
      }, 1000);
    }
    // e.preventDefault();
    return;
  }

  // console.log(event.originalEvent.wheelDelta)
});
