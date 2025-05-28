const navbarLink = document.querySelectorAll('li.menu');
if(navbarLink){
    navbarLink.forEach((item,index) => {
        const currentPage = window.location.pathname.split('/').pop().split('.')[0];
        if(item.id == currentPage || (item.id == 'index' && currentPage == '')){
            if(item.parentElement.classList.contains("submenu")){
                let submenu = item.parentElement;
                submenu.classList.add("show");
                submenu.parentElement.classList.add("active");
            }
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}
