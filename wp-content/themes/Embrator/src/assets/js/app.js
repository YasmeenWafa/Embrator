import $ from "jquery";
window.$ = $;
// Modules and Pages
import Foundation from "foundation-sites";
import "./lib/foundation-explicit-pieces";
import "./modules/header";
import "./modules/footer";
import "./modules/sliders";
import "./modules/contactUs";
import "./modules/cart-checkout";
import "./modules/about";
import "gsap/CSSPlugin.js";
import Parallax from "parallax-js";

$(document).foundation();
var scene = document.getElementById("scene");
var childNodes = scene.children;
//  console.log(childNodes);
var i;
for (i = 0; i < childNodes.length; i++) {
  var parallax = new Parallax(childNodes[i]);
}
