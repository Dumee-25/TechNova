// Dark Mode Toggle
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;

// Check for saved theme preference
const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
    body.setAttribute('data-theme', savedTheme);
    updateToggleIcon();
}

darkModeToggle.addEventListener('click', () => {
    if (body.getAttribute('data-theme') === 'dark') {
        body.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
    } else {
        body.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
    }
    updateToggleIcon();
});

function updateToggleIcon() {
    if (body.getAttribute('data-theme') === 'dark') {
        darkModeToggle.textContent = '☀️';
    } else {
        darkModeToggle.textContent = '🌙';
    }
}

// Mobile Navigation
const navToggle = document.querySelector('.nav-toggle');
const navMenu = document.querySelector('.nav-menu');

if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        navToggle.classList.toggle('active');
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
        navMenu.classList.remove('active');
        navToggle.classList.remove('active');
    }));
}

// Article Progress Bar
window.onscroll = function() { updateProgressBar() };

function updateProgressBar() {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    
    let progressBar = document.getElementById("progressBar");
    if (progressBar) {
        progressBar.style.width = scrolled + "%";
    }
}

// Initialize progress bar if on article page
if (window.location.pathname.includes('news.php')) {
    const progressContainer = document.createElement('div');
    progressContainer.className = 'progress-container';
    progressContainer.innerHTML = '<div class="progress-bar" id="progressBar"></div>';
    document.body.insertBefore(progressContainer, document.body.firstChild);
}

// Carousel functionality for homepage
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel-inner');
    if (carousel) {
        const items = carousel.querySelectorAll('.carousel-item');
        let currentIndex = 0;
        
        function showSlide(index) {
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }
        
        // Auto advance slides
        setInterval(() => {
            currentIndex = (currentIndex + 1) % items.length;
            showSlide(currentIndex);
        }, 5000);
    }
});
// Disable right-click context menu and text selection
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});
document.addEventListener('selectstart', function(e) {
    e.preventDefault();
});
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
});