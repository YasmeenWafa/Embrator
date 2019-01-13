$("td.product-quantity .plus-button").click(function(){
$(this).parent().parent().children('.quantity').children('.qty').val(Number($(this).parent().parent().children('.quantity').children('.qty').val())+1);
	$("[name='update_cart']").removeAttr('disabled')
	$("[name='update_cart']").trigger("click");
})
$("td.product-quantity .minus-button").click(function(){

  if($(this).parent().parent().children('.quantity').children('.qty').val()>1){
  $(this).parent().parent().children('.quantity').children('.qty').val(Number($(this).parent().parent().children('.quantity').children('.qty').val())-1);
  	$("[name='update_cart']").removeAttr("disabled");
  $("[name='update_cart']").trigger("click");
}

})

//adding placeholders to inputs in checkout Page
$("#billing_first_name_field input").attr("placeholder", "First Name");

$("#billing_last_name_field input").attr("placeholder", "Last Name");
$("#billing_company_field input").attr("placeholder", "Company");
$("#billing_address_1_field input").attr("placeholder", "Address");
$("#billing_city_field input").attr("placeholder", "City");
$("#billing_state_field input").attr("placeholder", "State");
$("#billing_postcode_field input").attr("placeholder", "Postal Code");
$("#billing_phone_field input").attr("placeholder", "Phone Number");
$("#order_comments_field textarea").attr("placeholder", "Order Notes");
$("#billing_email_field input").attr("placeholder", "Email Address");


$("#billing_phone_field input").attr("type", "number");



$(' .order-review-accordion .accordion-item').click(function(){

  if($(this).hasClass('is-active'))
  {
    //console.log($(this).children('.question').children('i'))
    $(this).children('.accordion-title').children('i').removeClass('icon-up-open');
    $(this).children('.accordion-title').children('i').addClass('icon-down-open');

  }
  else{
    $(this).children('.accordion-title').children('i').addClass('icon-up-open');
    $(this).children('.accordion-title').children('i').removeClass('icon-down-open');
  }


})
