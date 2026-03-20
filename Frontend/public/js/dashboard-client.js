document.addEventListener('DOMContentLoaded', function() {
    // --- SELECTORES ---
    const navLinks = document.querySelectorAll('.nav-link[data-section]');
    const sections = document.querySelectorAll('.section-content');
    const mainContent = document.querySelector('.main-content');

    // --- FUNCIÓN PARA MOSTRAR SECCIONES ---
    function showSection(sectionId) {
        // Ocultar todo
        sections.forEach(s => s.style.display = 'none');
        
        // Mostrar la correcta
        const target = document.getElementById(sectionId + '-section');
        if (target) {
            target.style.display = 'block';
            // Reset del scroll del contenedor principal cada vez que cambias
            mainContent.scrollTop = 0;
        }
    }

    // --- EVENTOS DE CLIC ---
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Actualizar estado activo en el menú
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // Cambiar sección
            const sectionName = this.getAttribute('data-section');
            showSection(sectionName);

            // Cerrar sidebar en móviles si está abierto
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    });

    // --- TOGGLE PARA MÓVILES (Hamburgesa) ---
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if(toggle) {
        toggle.onclick = () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        };
    }

    if(overlay) {
        overlay.onclick = () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        };
    }
});
