// Mobile Menu Toggle
document
  .querySelector(".mobile-menu-btn")
  .addEventListener("click", function () {
    document.querySelector(".nav-links").classList.toggle("active");
  });

// Navbar scroll effect
window.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar");
  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});
