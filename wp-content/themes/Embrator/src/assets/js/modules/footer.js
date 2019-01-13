//Footer Newsletter placeholder
$(".tnp-field.tnp-field-email>input").focus(function() {
  $(this)
    .siblings("label")
    .fadeOut();
});
$(".tnp-field.tnp-field-email>input").blur(function() {
  $(this)
    .siblings("label")
    .fadeIn();
});
