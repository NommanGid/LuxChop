        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Three.js Scene Setup
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('canvas-container').appendChild(renderer.domElement);

        // Create a bowl shape
        const bowlGeometry = new THREE.Group();

        // Rice portion (slightly domed surface)
        const riceGeometry = new THREE.SphereGeometry(2.2, 32, 32, 0, Math.PI * 2, 0, Math.PI * 0.5);
        const textureLoader = new THREE.TextureLoader();
        const riceTexture = textureLoader.load('friedrice.jpg');
        const riceMaterial = new THREE.MeshStandardMaterial({
            map: riceTexture,
            roughness: 0.8,
            metalness: 0.2,
            bumpMap: riceTexture,
            bumpScale: 0.1
        });
        const rice = new THREE.Mesh(riceGeometry, riceMaterial);
        rice.rotation.x = Math.PI;
        rice.position.y = 0.5;

        // Bowl exterior
        const bowlExteriorGeometry = new THREE.SphereGeometry(2.2, 32, 32, 0, Math.PI * 2, 0, Math.PI * 0.6);
        const bowlMaterial = new THREE.MeshPhongMaterial({
            color: 0xffffff,
            shininess: 100,
            specular: 0x444444,
            side: THREE.DoubleSide
        });
        const bowlExterior = new THREE.Mesh(bowlExteriorGeometry, bowlMaterial);
        bowlExterior.rotation.x = Math.PI;
        
        // Add everything to the group
        bowlGeometry.add(rice);
        bowlGeometry.add(bowlExterior);

        // Tilt the bowl slightly
        bowlGeometry.rotation.x = -0.3;
        scene.add(bowlGeometry);

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);

        const frontLight = new THREE.DirectionalLight(0xffffff, 1);
        frontLight.position.set(0, 5, 5);
        scene.add(frontLight);

        const backLight = new THREE.DirectionalLight(0xffffff, 0.5);
        backLight.position.set(0, 5, -5);
        scene.add(backLight);

        // Add some point lights for specular highlights
        const pointLight1 = new THREE.PointLight(0xffffff, 0.5);
        pointLight1.position.set(5, 5, 5);
        scene.add(pointLight1);

        const pointLight2 = new THREE.PointLight(0xffffff, 0.5);
        pointLight2.position.set(-5, 5, -5);
        scene.add(pointLight2);

        camera.position.z = 8;

        // Animation
        let mouseX = 0;
        let mouseY = 0;
        const windowHalfX = window.innerWidth / 2;
        const windowHalfY = window.innerHeight / 2;

        document.addEventListener('mousemove', (event) => {
            mouseX = (event.clientX - windowHalfX) / 100;
            mouseY = (event.clientY - windowHalfY) / 100;
        });

        function animate() {
            requestAnimationFrame(animate);

            // Smooth rotation following mouse
            bowlGeometry.rotation.y += (mouseX - bowlGeometry.rotation.y) * 0.05;
            bowlGeometry.rotation.x += (mouseY - bowlGeometry.rotation.x) * 0.05;

            // Add subtle floating animation
            bowlGeometry.position.y = Math.sin(Date.now() * 0.001) * 0.2;

            renderer.render(scene, camera);
        }
        animate();

        // Handle window resize
        window.addEventListener('resize', onWindowResize, false);
        function onWindowResize() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            windowHalfX = window.innerWidth / 2;
            windowHalfY = window.innerHeight / 2;
        }

        // Countdown Timer
        function updateCountdown() {
            const now = new Date();
            const midnight = new Date();
            midnight.setHours(24, 0, 0, 0);
            
            const diff = midnight - now;
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();

        // Promotion Slider
        let currentSlide = 0;
        const slider = document.getElementById('promoSlider');
        const slides = document.querySelectorAll('.promo-slide');
        const totalSlides = slides.length;

        function moveSlide(direction) {
            currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        // Auto-slide every 5 seconds
        setInterval(() => moveSlide(1), 5000);

        // Delivery Schedule Status
        function updateDeliveryStatus() {
            const now = new Date();
            const day = now.getDay(); // 0 is Sunday, 6 is Saturday
            const hour = now.getHours();
            const minute = now.getMinutes();
            const currentTime = hour * 60 + minute;

            const weekdayStatus = document.getElementById('weekdayStatus');
            const weekendStatus = document.getElementById('weekendStatus');

            // Weekday logic
            if (day >= 1 && day <= 5) { // Monday to Friday
                if ((currentTime >= 660 && currentTime <= 900) || // 11:00 AM - 3:00 PM
                    (currentTime >= 1080 && currentTime <= 1260)) { // 6:00 PM - 9:00 PM
                    weekdayStatus.innerHTML = '<span class="status-open"> Currently Open</span>';
                } else {
                    weekdayStatus.innerHTML = '<span class="status-closed">🔴 Currently Closed</span>';
                }
            }

            // Weekend logic
            if (day === 0 || day === 6) { // Saturday or Sunday
                if ((currentTime >= 600 && currentTime <= 960) || // 10:00 AM - 4:00 PM
                    (currentTime >= 1020 && currentTime <= 1320)) { // 5:00 PM - 10:00 PM
                    weekendStatus.innerHTML = '<span class="status-open">🟢 Currently Open</span>';
                } else {
                    weekendStatus.innerHTML = '<span class="status-closed">🔴 Currently Closed</span>';
                }
            }
        }

        // Update status every minute
        setInterval(updateDeliveryStatus, 60000);
        updateDeliveryStatus();

        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('navbar');
            const mobileMenu = document.getElementById('mobile-menu');
            const navMenu = document.querySelector('.nav-menu');
            const navLinks = document.querySelectorAll('.nav-link');

            // Mobile menu toggle
            mobileMenu.addEventListener('click', function() {
                this.classList.toggle('active');
                navMenu.classList.toggle('active');
            });

            // Smooth scrolling for nav links
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href');
                        const targetSection = document.querySelector(targetId);
                        
                        if (targetSection) {
                            // Close mobile menu if open
                            mobileMenu.classList.remove('active');
                            navMenu.classList.remove('active');

                            // Smooth scroll to section
                            targetSection.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });

            // Change navbar style on scroll
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
        // Number counter animation
function animateNumbers() {
    const numbers = document.querySelectorAll('.stat-number');
    
    numbers.forEach(number => {
        const target = parseInt(number.getAttribute('data-count'));
        let count = 0;
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps

        const updateCount = () => {
            count += increment;
            if (count < target) {
                number.textContent = Math.round(count);
                requestAnimationFrame(updateCount);
            } else {
                number.textContent = target;
            }
        };

        // Start animation when element is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCount();
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(number);
    });
}

// Call the function when document is loaded
document.addEventListener('DOMContentLoaded', animateNumbers);

// FAQ Functionality
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement;
        faqItem.classList.toggle('active');
    });
});

// Newsletter Form
document.querySelector('.newsletter-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    // Add your newsletter subscription logic here
    alert('Thank you for subscribing!');
    this.reset();
});

// Order Tracking Form
document.querySelector('.tracking-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const orderNumber = this.querySelector('input[type="text"]').value;
    // Add your order tracking logic here
    alert('Tracking information will be sent to you shortly.');
    this.reset();
});

// Gallery Image Loading
document.querySelectorAll('.gallery-item img').forEach(img => {
    img.addEventListener('load', function() {
        this.parentElement.classList.add('loaded');
    });
});

// Lazy Loading for Images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Smooth Scroll for All Internal Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form Validation Enhancement
const forms = document.querySelectorAll('form');
forms.forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            Array.from(this.elements).forEach(input => {
                if (!input.validity.valid) {
                    input.classList.add('invalid');
                }
            });
        }
    });
});

// Add loading state to buttons
document.querySelectorAll('button').forEach(button => {
    button.addEventListener('click', function() {
        if (this.form && !this.form.checkValidity()) return;
        this.classList.add('loading');
        setTimeout(() => {
            this.classList.remove('loading');
        }, 2000);
    });
});

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }

        // Dark Mode Toggle
        document.addEventListener('DOMContentLoaded', () => {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
            
            // Check for saved user preference, first in localStorage, then in system preferences
            const getCurrentTheme = () => {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    return savedTheme;
                }
                return prefersDarkScheme.matches ? 'dark' : 'light';
            }

            const setTheme = (theme) => {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                
                // Update toggle button icon
                const icon = darkModeToggle.querySelector('i');
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }

            // Set initial theme
            setTheme(getCurrentTheme());

            // Toggle theme on button click
            darkModeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });

            // Listen for system theme changes
            prefersDarkScheme.addEventListener('change', (e) => {
                const newTheme = e.matches ? 'dark' : 'light';
                setTheme(newTheme);
            });
        });