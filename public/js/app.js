/**
 * PANASONIC ELECTRIC PRO - APP INTERACTIVE SCRIPTS
 * Xử lý: Mobile Drawer, Hero Slider, Toast alerts, MCB Calculator, Counter, Drag & Drop BOM
 */

(function () {
    'use strict';

    // -------------------------------------------------------------
    // 1. Toast Notification Helper
    // -------------------------------------------------------------
    window.showToast = function (message, type = 'success') {
        const stack = document.getElementById('toastStack');
        if (!stack) return;

        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.innerHTML = `
            <span>${message}</span>
            <button type="button" style="background:transparent;border:none;cursor:pointer;margin-left:auto;" onclick="this.parentElement.remove()">✕</button>
        `;
        stack.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    // -------------------------------------------------------------
    // 2. Mobile Drawer Navigation
    // -------------------------------------------------------------
    const drawer = document.getElementById('mobileDrawer');
    const backdrop = document.querySelector('.drawer-backdrop');
    const openBtns = document.querySelectorAll('[data-drawer-open]');
    const closeBtns = document.querySelectorAll('[data-drawer-close]');

    function toggleDrawer(open) {
        if (!drawer || !backdrop) return;
        drawer.classList.toggle('is-active', open);
        backdrop.classList.toggle('is-active', open);
        document.body.style.overflow = open ? 'hidden' : '';
    }

    openBtns.forEach(btn => btn.addEventListener('click', () => toggleDrawer(true)));
    closeBtns.forEach(btn => btn.addEventListener('click', () => toggleDrawer(false)));

    // -------------------------------------------------------------
    // 3. Hero Slider Carousel (Banner Động Panasonic)
    // -------------------------------------------------------------
    const slider = document.querySelector('[data-hero-slider], [data-slider]');
    if (slider) {
        const slides = slider.querySelectorAll('.hero-banner-slide, .hero__slide');
        const dots = slider.querySelectorAll('.hero-banner-dot, [data-slider-dot]');
        const prevBtn = slider.querySelector('[data-slider-prev]');
        const nextBtn = slider.querySelector('[data-slider-next]');
        let current = 0;
        let timer = null;
        const interval = parseInt(slider.dataset.interval, 10) || 4500;

        function goTo(index) {
            if (!slides.length) return;
            slides[current]?.classList.remove('is-active');
            dots[current]?.classList.remove('is-active');
            current = (index + slides.length) % slides.length;
            slides[current]?.classList.add('is-active');
            dots[current]?.classList.add('is-active');
        }

        function startAuto() {
            stopAuto();
            if (slides.length > 1) {
                timer = setInterval(() => goTo(current + 1), interval);
            }
        }

        function stopAuto() {
            if (timer) clearInterval(timer);
        }

        prevBtn?.addEventListener('click', (e) => { 
            e.preventDefault();
            goTo(current - 1); 
            startAuto(); 
        });

        nextBtn?.addEventListener('click', (e) => { 
            e.preventDefault();
            goTo(current + 1); 
            startAuto(); 
        });

        dots.forEach((dot, idx) => dot.addEventListener('click', (e) => { 
            e.preventDefault();
            goTo(idx); 
            startAuto(); 
        }));

        slider.addEventListener('mouseenter', stopAuto);
        slider.addEventListener('mouseleave', startAuto);
        startAuto();
    }

    // -------------------------------------------------------------
    // 4. Counter Animation (Hero Stats)
    // -------------------------------------------------------------
    const countItems = document.querySelectorAll('[data-count]');
    if ('IntersectionObserver' in window && countItems.length) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.count, 10);
                    let count = 0;
                    const step = Math.ceil(target / 40);
                    const update = () => {
                        count += step;
                        if (count >= target) {
                            el.textContent = target.toLocaleString('vi-VN');
                        } else {
                            el.textContent = count.toLocaleString('vi-VN');
                            requestAnimationFrame(update);
                        }
                    };
                    update();
                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        countItems.forEach(el => observer.observe(el));
    }

    // -------------------------------------------------------------
    // 5. MCB / RCBO Calculator (Công cụ tra cứu tải Ampe)
    // -------------------------------------------------------------
    const mcbForm = document.querySelector('[data-mcb-calc]');
    if (mcbForm) {
        const breakers = JSON.parse(mcbForm.dataset.breakers || '[]');
        const outCurrent = mcbForm.querySelector('[data-out="current"]');
        const outRating = mcbForm.querySelector('[data-out="rating"]');
        const outWire = mcbForm.querySelector('[data-out="wire"]');
        const outPoles = mcbForm.querySelector('[data-out="poles"]');
        const outProducts = mcbForm.querySelector('[data-out="products"]');

        function calculate() {
            const power = parseFloat(mcbForm.power.value) || 0;
            const phase = parseInt(mcbForm.phase.value, 10);
            const pf = parseFloat(mcbForm.pf.value) || 0.9;
            const usage = mcbForm.usage.value;

            if (power <= 0) return;

            // Tính dòng làm việc Ib
            let ib = 0;
            if (phase === 1) {
                ib = power / (220 * pf);
            } else {
                ib = power / (Math.sqrt(3) * 380 * pf);
            }

            // Dòng chọn cầu dao: In >= 1.25 * Ib
            const targetIn = ib * 1.25;
            const standardRatings = [6, 10, 16, 20, 25, 32, 40, 50, 63, 80, 100];
            let selectedIn = standardRatings.find(r => r >= targetIn) || standardRatings[standardRatings.length - 1];

            // Tiết diện dây đồng Cu (mật độ dòng J = 4-6 A/mm2)
            let wireSize = '1.5 mm²';
            if (selectedIn <= 10) wireSize = '1.5 mm²';
            else if (selectedIn <= 16) wireSize = '2.5 mm²';
            else if (selectedIn <= 25) wireSize = '4.0 mm²';
            else if (selectedIn <= 32) wireSize = '6.0 mm²';
            else if (selectedIn <= 50) wireSize = '10.0 mm²';
            else wireSize = '16.0 mm² trở lên';

            const polesRec = phase === 1 ? (usage === 'rcbo' ? '2P (1P+N)' : '1P hoặc 2P') : '3P hoặc 4P';

            // Xuất kết quả
            if (outCurrent) outCurrent.textContent = `${ib.toFixed(1)} A`;
            if (outRating) outRating.textContent = `${selectedIn} A`;
            if (outWire) outWire.textContent = `Cu/PVC ${wireSize}`;
            if (outPoles) outPoles.textContent = polesRec;

            // Gợi ý sản phẩm thực tế từ Database
            if (outProducts && breakers.length) {
                const match = breakers.filter(b => {
                    const matchAmp = Math.abs(b.amp - selectedIn) <= 4;
                    const matchPole = phase === 1 ? (b.poles === 1 || b.poles === 2) : b.poles === 3;
                    return matchAmp && matchPole;
                }).slice(0, 2);

                if (match.length) {
                    outProducts.innerHTML = `
                        <div style="font-size:0.75rem; color:#94a3b8; margin-bottom:4px;">Thiết bị Panasonic tương thích:</div>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            ${match.map(m => `
                                <a href="${m.url}" target="_blank" style="background:rgba(255,255,255,0.08); padding:6px 10px; border-radius:4px; font-size:0.8rem; color:#fff; display:flex; justify-content:space-between;">
                                    <span><strong>${m.model}</strong> - ${m.name}</span>
                                    <strong style="color:#38bdf8;">${m.price}</strong>
                                </a>
                            `).join('')}
                        </div>
                    `;
                } else {
                    outProducts.innerHTML = '';
                }
            }
        }

        mcbForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', calculate);
            input.addEventListener('change', calculate);
        });

        // Nút preset
        mcbForm.querySelectorAll('[data-preset]').forEach(btn => {
            btn.addEventListener('click', () => {
                mcbForm.power.value = btn.dataset.preset;
                calculate();
            });
        });

        calculate();
    }

    // -------------------------------------------------------------
    // 6. Kéo thả Upload File (Drag & Drop BOM)
    // -------------------------------------------------------------
    document.querySelectorAll('[data-dropzone]').forEach(zone => {
        const input = zone.querySelector('.dropzone__input');
        const fileLabel = zone.querySelector('[data-dropzone-file]');

        ['dragenter', 'dragover'].forEach(name => {
            zone.addEventListener(name, (e) => { e.preventDefault(); zone.classList.add('is-dragover'); });
        });
        ['dragleave', 'drop'].forEach(name => {
            zone.addEventListener(name, (e) => { e.preventDefault(); zone.classList.remove('is-dragover'); });
        });

        zone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                updateFile(e.dataTransfer.files[0]);
            }
        });

        input?.addEventListener('change', () => {
            if (input.files.length) updateFile(input.files[0]);
        });

        function updateFile(file) {
            if (!fileLabel) return;
            fileLabel.hidden = false;
            fileLabel.textContent = `✓ Đã chọn file: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        }
    });

    // -------------------------------------------------------------
    // 7. AJAX Submit Form (Báo giá công trình)
    // -------------------------------------------------------------
    document.querySelectorAll('[data-ajax-form]').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Đang gửi thông tin...';

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success) {
                    window.showToast(data.message, 'success');
                    form.reset();
                    const fileLabel = form.querySelector('[data-dropzone-file]');
                    if (fileLabel) fileLabel.hidden = true;
                } else {
                    window.showToast(data.message || 'Có lỗi xảy ra, vui lòng kiểm tra lại.', 'error');
                }
            } catch (err) {
                window.showToast('Không thể kết nối máy chủ, vui lòng thử lại.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });

    // -------------------------------------------------------------
    // 8. Nút Back to top & Smart Sticky Header
    // -------------------------------------------------------------
    const backTop = document.querySelector('[data-back-top]');
    if (backTop) {
        window.addEventListener('scroll', () => {
            backTop.classList.toggle('is-visible', window.scrollY > 400);
        });
        backTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Smart Sticky Header: Kéo xuống thì từ từ kéo lên ẩn đi, kéo lên thì từ từ kéo xuống hiện ra
    const siteHeader = document.getElementById('siteHeader');
    if (siteHeader && !window.__headerScrollBound) {
        let lastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
        let isHidden = false;
        const threshold = 4;

        window.addEventListener('scroll', () => {
            const currentY = Math.max(0, window.pageYOffset || document.documentElement.scrollTop || 0);

            if (currentY <= 30) {
                if (isHidden) {
                    siteHeader.classList.remove('header--hidden');
                    isHidden = false;
                }
                siteHeader.classList.remove('header--scrolled');
                lastScrollY = currentY;
                return;
            }

            siteHeader.classList.add('header--scrolled');
            const diff = currentY - lastScrollY;

            if (Math.abs(diff) < threshold) {
                return;
            }

            // Kéo xuống -> ẩn menu
            if (diff > 0 && currentY > 50) {
                if (!isHidden) {
                    siteHeader.classList.add('header--hidden');
                    isHidden = true;
                    const searchDropdown = document.getElementById('searchResults');
                    if (searchDropdown) {
                        searchDropdown.style.display = 'none';
                        searchDropdown.setAttribute('hidden', '');
                    }
                }
            }
            // Kéo lên -> hiện menu
            else if (diff < 0) {
                if (isHidden) {
                    siteHeader.classList.remove('header--hidden');
                    isHidden = false;
                }
            }

            lastScrollY = currentY;
        }, { passive: true });
        window.__headerScrollBound = true;
    }

    // Dynamic Hero Banner Carousel Controller
    const heroSlider = document.querySelector('[data-hero-slider]');
    if (heroSlider) {
        const slides = heroSlider.querySelectorAll('.hero-slide');
        const dots = heroSlider.querySelectorAll('.hero-dot');
        const prevBtn = heroSlider.querySelector('.hero-slider-nav--prev');
        const nextBtn = heroSlider.querySelector('.hero-slider-nav--next');
        let currentIdx = 0;
        let autoPlayTimer = null;

        function setHeroSlide(idx) {
            if (!slides.length) return;
            slides[currentIdx]?.classList.remove('is-active');
            dots[currentIdx]?.classList.remove('is-active');
            currentIdx = (idx + slides.length) % slides.length;
            slides[currentIdx]?.classList.add('is-active');
            dots[currentIdx]?.classList.add('is-active');
        }

        function startHeroPlay() {
            stopHeroPlay();
            autoPlayTimer = setInterval(() => setHeroSlide(currentIdx + 1), 5000);
        }

        function stopHeroPlay() {
            if (autoPlayTimer) clearInterval(autoPlayTimer);
        }

        prevBtn?.addEventListener('click', () => { setHeroSlide(currentIdx - 1); startHeroPlay(); });
        nextBtn?.addEventListener('click', () => { setHeroSlide(currentIdx + 1); startHeroPlay(); });
        dots.forEach((dot, i) => dot.addEventListener('click', () => { setHeroSlide(i); startHeroPlay(); }));

        heroSlider.addEventListener('mouseenter', stopHeroPlay);
        heroSlider.addEventListener('mouseleave', startHeroPlay);
        startHeroPlay();
    }

    // Tự ẩn flash thông báo sau 5 giây
    document.querySelectorAll('[data-autohide]').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    document.querySelectorAll('[data-dismiss]').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.flash')?.remove());
    });

})();
