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
<<<<<<< HEAD
import "./modules/loadMore";
=======
import "./modules/about";
import "./modules/b2b";
>>>>>>> 2045b59cecf42cefc6b852b6c25291ae2a401810
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
