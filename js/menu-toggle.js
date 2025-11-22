// Common Menu Toggle Functionality

// Toggle mobile menu
function toggleMenu() {
    const menu = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('menuOverlay');
    if (menu) menu.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}

// Close menu when clicking outside on mobile
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('sidebarMenu');
        const toggle = document.querySelector('.menu-toggle');
        const overlay = document.getElementById('menuOverlay');
        
        if (window.innerWidth <= 768) {
            if (menu && !menu.contains(event.target) && toggle && !toggle.contains(event.target) && overlay && overlay.contains(event.target)) {
                menu.classList.remove('active');
                overlay.classList.remove('active');
            }
        }
    });
});











