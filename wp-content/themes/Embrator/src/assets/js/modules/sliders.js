import slick from "slick-carousel";
// import { TweenLite, TimelineLite } from "gsap";
import "gsap";

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
  console.log(slideOut);
  console.log(slideIn);
  goToNextSlide(slideOut, slideIn);
});

// Navigation click - go to the Previous Slide
$slideNavPrev.click(function(e) {
  e.preventDefault();

  var slideOut = $(".bannerSlider-figure.active"),
    slideIn = $(".bannerSlider-figure.active").prev(".bannerSlider-figure");

  goToPreviousSlide(slideOut, slideIn);
});

var h = $("#ver").outerHeight(),
  h2 = $("#sec2").outerHeight();

var last_known_scroll_position = 0,
  ticking = false,
  lastScrollPosition = 0;

let sliding = false;
$(document).on("wheel mousewheel", function(event) {
  if (sliding) return;
  let deltaY = event.originalEvent.deltaY;
  let last_known_scroll_position = window.scrollY;
  // console.log(h);
  if (last_known_scroll_position < h) {
    // console.log("last_known_scroll_position =" + last_known_scroll_position);

    $("#ver").css("position", "fixed");

    $("body").addClass("preventScroll");
    if (Math.abs(deltaY) > 50) {
      sliding = true;
      if (deltaY > 0) {
        // Check if the user reached the last slide or not , if yes so when he scroll down the page should scroll down normally

        if ($(".lastSlide").hasClass("active")) {
          $("#ver").css("position", "relative");
          $("body").removeClass("preventScroll");
          // alert("up");
          $("html,body").animate(
            {
              scrollTop: $("#sec2").offset().top
            },
            1000
          );
        }

        var slideOut = $(".bannerSlider-figure.active"),
          slideIn = $(".bannerSlider-figure.active").next(
            ".bannerSlider-figure"
          );

        goToNextSlide(slideOut, slideIn);
      }

      if (deltaY < 0) {
        // Sliding Down
        // alert("down");
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
    return;
  } else if (
    last_known_scroll_position >= h &&
    last_known_scroll_position < h + h2 / 2
  ) {
    // IF the user is in the top part of section 2 and scrolled up it will get him to the top of the slider
    if (deltaY < 0) {
      $("html,body").animate(
        {
          scrollTop: $("#cont").offset().top
        },
        1000
      );

      var slideOut = $(".bannerSlider-figure.active"),
        slideIn = $(`#cat3.slide01`);
      goToNextSlide(slideOut, slideIn);
    }
  }
});
$(document).on("click", ".categoryVariation", function(e) {
  e.preventDefault();
  // alert("clicked");
  var number = $(this).attr("catNumber"),
    slideOut = $(".bannerSlider-figure.active"),
    slideIn = $(`#cat${number} .slide01`);
  // if the user clicked on the category while it's aleardy active and in the first slide do nothing
  if (slideIn.hasClass("active")) {
    // alert("daaah");
  } else {
    $(".categoryVariation").removeClass("active");
    $(this).addClass("active");

    $(".cat").css("z-index", "0");
    $(`#cat${number}`).css("z-index", "1");
    goToNextSlide(slideOut, slideIn);
  }
});
