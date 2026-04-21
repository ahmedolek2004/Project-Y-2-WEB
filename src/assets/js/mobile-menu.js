// Mobile Menu Toggle & Sidebar Collapse
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    
    // Sidebar collapse/expand for desktop
    if (sidebarToggle && window.innerWidth > 768) {
        // Load saved state from localStorage
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed && sidebar) {
            sidebar.classList.add('collapsed');
        }
        
        // Toggle sidebar on button click
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (sidebar) {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        });
    }
    
    // Only add mobile menu on smaller screens
    if (sidebar && window.innerWidth <= 768 && !document.querySelector('.mobile-menu-toggle')) {
        const toggle = document.createElement('button');
        toggle.className = 'mobile-menu-toggle';
        toggle.setAttribute('aria-label', 'Toggle menu');
        toggle.innerHTML = '☰';
        document.body.appendChild(toggle);
        
        // Create backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
        
        // Toggle menu on button click
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            backdrop.classList.toggle('active');
        });
        
        // Close menu when clicking on backdrop
        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('active');
            backdrop.classList.remove('active');
        });
        
        // Close menu when clicking on a nav link
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
            });
        });
        
        // Close menu when clicking on logout button
        const logoutBtn = sidebar.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
            });
        }
        
        // Close menu on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
            }
        });
    }
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const width = window.innerWidth;
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.querySelector('.sidebar-backdrop');
            const toggle = document.querySelector('.mobile-menu-toggle');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            
            if (width > 768) {
                // On large screens: make sidebar visible, hide toggle
                if (sidebar) {
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(0)';
                    sidebar.style.display = 'flex';
                }
                if (backdrop) {
                    backdrop.classList.remove('active');
                    backdrop.style.display = 'none';
                }
                if (toggle) {
                    toggle.style.display = 'none';
                }
                if (sidebarToggle && !toggle) {
                    sidebarToggle.style.display = 'flex';
                }
            } else {
                // On small screens: hide sidebar by default, show toggle
                if (sidebar) {
                    sidebar.classList.remove('active');
                    sidebar.classList.remove('collapsed');
                    sidebar.style.transform = 'translateX(-100%)';
                    sidebar.style.display = 'flex';
                }
                if (toggle) {
                    toggle.style.display = 'flex';
                }
                if (sidebarToggle) {
                    sidebarToggle.style.display = 'none';
                }
            }
        }, 250);
    });
});

// Improve form inputs on mobile
document.addEventListener('DOMContentLoaded', function() {
    // Prevent iOS zoom on input focus
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (window.innerWidth < 768) {
                // Adjust viewport on focus for better UX
                setTimeout(() => {
                    window.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
    });
});

// Add smooth scrolling for better mobile experience
document.addEventListener('click', function(e) {
    const href = e.target.getAttribute('href');
    if (href && href.startsWith('#')) {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

