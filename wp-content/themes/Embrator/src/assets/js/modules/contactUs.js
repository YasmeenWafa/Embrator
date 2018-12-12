
let screenHeight = $('body').height();
let footerHeight = $('footer').outerHeight();
// console.log(footerHeight)
let triangleHeight = screenHeight-footerHeight;
$(document).ready(function(){
  // console.log('hi')
  let width;
  if($(window).width() > 1024){
    width = '0 0 '+ triangleHeight+'px 40vw';
  }else{
      width = '0 0 '+ triangleHeight+'px 50vw';
  }
  $('.contact-us-container .blue-triangle').css({
    'border-width': width
  })
})
