const topBtn = document.getElementById("topBtn");
const navBar = document.querySelector("header");

function gérerTopBtn(btn = topBtn, navbar = navBar) {
  if (!btn || !navbar) return;

  function updateButtonVisibility() {
    const rect = navbar.getBoundingClientRect();

    btn.style.display = rect.top < 0 ? "block" : "none";
  }

  window.addEventListener("scroll", updateButtonVisibility);
  window.addEventListener("resize", updateButtonVisibility);
  window.addEventListener("load", updateButtonVisibility);

  btn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

gérerTopBtn(topBtn, navBar);


const burg = document.getElementById("burger");
const nav = document.getElementById("nav_burg");


burg.addEventListener("click", () =>{
  if (nav.style.display === "flex"){
    nav.style.display = "none";
  }
  else {
    nav.style.display = "flex";
  }
});