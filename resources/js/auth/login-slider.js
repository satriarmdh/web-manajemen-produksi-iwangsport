document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.loginSlides === 'undefined') return;
    
    const originalSlides = window.loginSlides;

    const track = document.getElementById('slider-track');
    const sliderText = document.getElementById('slider-text');
    const dotsContainer = document.getElementById('slider-dots');
    
    // Variabel state
    let currentIndex = 0;
    let isTransitioning = false;
    let slideInterval;
    
    const totalSlides = originalSlides.length;

    // 1. Tanamkan gambar ke track (Termasuk Kloning)
    const slidesToRender = [...originalSlides, originalSlides[0]];

    slidesToRender.forEach((slide) => {
        const bgDiv = document.createElement('div');
        bgDiv.className = 'min-w-full h-full bg-cover bg-center';
        bgDiv.style.backgroundImage = `url('${slide.image}')`;
        track.appendChild(bgDiv);
    });

    // 2. Buat titik (dots) sejumlah slide asli
    function renderDots() {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('span');
            const activeIndex = currentIndex === totalSlides ? 0 : currentIndex;
            const widthClass = i === activeIndex ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/60';
            
            dot.className = `h-2 rounded-full cursor-pointer transition-all duration-500 ease-in-out ${widthClass}`;
            dot.onclick = () => {
                if(!isTransitioning) goToSlide(i);
            };
            dotsContainer.appendChild(dot);
        }
    }

    // 3. Fungsi utama menggeser slide
    function goToSlide(index) {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentIndex = index;
        
        // Geser track
        track.style.transition = 'transform 700ms ease-in-out';
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        renderDots();
        
        // Animasi Teks
        sliderText.classList.remove('translate-y-0', 'opacity-100');
        sliderText.classList.add('translate-y-4', 'opacity-0');
        
        setTimeout(() => {
            const targetTextIndex = currentIndex === totalSlides ? 0 : currentIndex;
            sliderText.textContent = originalSlides[targetTextIndex].text;
            
            sliderText.classList.remove('translate-y-4', 'opacity-0');
            sliderText.classList.add('translate-y-0', 'opacity-100');
        }, 300);

        // Reset Infinite Loop
        if (currentIndex === totalSlides) {
            setTimeout(() => {
                track.style.transition = 'none';
                currentIndex = 0;
                track.style.transform = `translateX(0)`;
                
                setTimeout(() => {
                    isTransitioning = false;
                }, 50);
            }, 700); 
        } else {
            setTimeout(() => {
                isTransitioning = false;
            }, 700);
        }
    }

    // 4. Perintah Next Otomatis
    function startSlide() {
        slideInterval = setInterval(() => {
            goToSlide(currentIndex + 1);
        }, 4000);
    }

    // Eksekusi
    renderDots();
    startSlide();
});