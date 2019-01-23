// import $ from "jquery";

$(".tabs-title").on("hover mouseover", function() {
  $(this).trigger("click");
});

var sliderSectionHight = $("#ver").outerHeight();
$(window).scroll(function() {
  var top = $(this).scrollTop();
  // If we are in the homepage do this 
  if ($("body").hasClass("page-id-5")) {
    if (top > sliderSectionHight) {
      $(".embrator__header").addClass("header--colored");
    } else {
      $(".embrator__header").removeClass("header--colored");
    }
  }else {
    // If we are in any other page than homepage change the background color of the header 
    if (top > 0) {
      $(".embrator__header").addClass("header--colored");
    } else {
      $(".embrator__header").removeClass("header--colored");
    }
  }
});
$("#nav-icon").click(function() {
  $(this).toggleClass("open");
  $(".main__menu").toggleClass("main__menu--open");
  $(".nav-bar").toggleClass("nav-bar--open");
  $("body").toggleClass("preventScroll");
});
// Toggle the top menu

// console.log(headerHight);

// position of the popup

$(".menu-item-256").click(function(e) {
  var headerHight = $(".embrator__header--desktop").outerHeight();
  $("#topMenu").css("top", headerHight);
  // $(window).resize(function() {
  // alert($(window).width());
  if ($(window).width() >= 1024) {
    e.preventDefault();
    $("#topMenu").fadeToggle();
  }
  // });
});

// $(window).resize(function() {
//   headerHight = $(".embrator__header--desktop").outerHeight();
//   $("#topMenu").css("top", headerHight);
//   $(".menu-item-21").click(function(e) {
//     // alert($(window).width());
//     if ($(window).width() >= 1024) {
//       e.preventDefault();
//       $("#topMenu").fadeToggle();
//     }
//   });
// });
