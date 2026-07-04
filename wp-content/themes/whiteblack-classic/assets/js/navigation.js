document.addEventListener('DOMContentLoaded', function () {
    // Toggle del menu principale
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.main-navigation .menu');

    menuToggle.addEventListener('click', function () {
        menu.classList.toggle('open');
    });

    // Gestione dei sottomenu
    const menuItemsWithSubmenus = document.querySelectorAll('.main-navigation .menu li');
    menuItemsWithSubmenus.forEach(function(item) {
        item.addEventListener('click', function (e) {
            const submenu = item.querySelector('ul');
            if (submenu) {
                submenu.classList.toggle('open');
                // Impediamo che il click sul sottomenu propaghi (così non chiude il menu principale)
                e.stopPropagation();
            }
        });
    });

    // Chiudi il sottomenu se si clicca all'esterno
    document.addEventListener('click', function (e) {
        const isClickInsideMenu = menu.contains(e.target);
        if (!isClickInsideMenu) {
            // Chiudi tutti i sottomenu
            document.querySelectorAll('.main-navigation .menu li ul').forEach(function(submenu) {
                submenu.classList.remove('open');
            });
        }
    });
});
