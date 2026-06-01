// Dashboard Sidebar Toggle Script

document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    // Create overlay for mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    // Toggle sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            
            // On mobile, show overlay when sidebar is open
            if (window.innerWidth <= 991) {
                overlay.classList.toggle('active');
            } else {
                content.classList.toggle('active');
            }
        });
    }

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    // Handle Bootstrap collapse dropdowns
    const dropdownToggles = document.querySelectorAll('[data-toggle="collapse"]');
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                // Toggle the collapse
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    target.classList.add('show');
                    this.setAttribute('aria-expanded', 'true');
                }
            }
        });
    });

    // Close sidebar on window resize if needed
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            overlay.classList.remove('active');
            if (sidebar.classList.contains('active')) {
                content.classList.add('active');
            }
        } else {
            content.classList.remove('active');
        }
    });
});