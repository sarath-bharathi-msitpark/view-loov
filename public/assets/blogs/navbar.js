window.addEventListener('scroll', function () {
    const logo = document.getElementById('navbar-logo');
    const defaultSrc = logo.getAttribute('src');
    const scrollSrc = logo.getAttribute('data-src');

    if (window.scrollY > 20) {
        logo.setAttribute('src', scrollSrc);
    } else {
        logo.setAttribute('src', defaultSrc);
    }
});


function setActiveLink() {
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

  navLinks.forEach(link => {
    const path = link.getAttribute('data-path');
    if (path === currentPath) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
}

setActiveLink();


window.addEventListener('popstate', setActiveLink);

