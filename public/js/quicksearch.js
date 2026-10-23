/**
 * PANASONIC ELECTRIC PRO - QUICK SEARCH (LIVE SEARCH AJAX)
 * Gợi ý tức thì khi gõ Model Code Panasonic: WEV, BBD, Moderva, F-60, ổ cắm, đèn...
 */

(function () {
    'use strict';

    const searchForms = document.querySelectorAll('[data-quicksearch]');

    searchForms.forEach(form => {
        const input = form.querySelector('.search__input') || form.querySelector('input[type="search"]') || form.querySelector('.hsearch-input');
        const dropdown = form.querySelector('.search__dropdown') || form.querySelector('#searchResults');
        if (!input || !dropdown) return;

        let debounceTimer = null;
        let activeIndex = -1;

        function showDropdown() {
            dropdown.hidden = false;
            dropdown.removeAttribute('hidden');
            dropdown.style.display = 'block';
            dropdown.style.visibility = 'visible';
            dropdown.style.opacity = '1';
        }

        function hideDropdown() {
            dropdown.hidden = true;
            dropdown.setAttribute('hidden', '');
            dropdown.style.display = 'none';
        }

        async function doSearch(query) {
            try {
                const searchUrl = `${window.APP?.routes?.search || '/api/tim-kiem'}?q=${encodeURIComponent(query)}`;
                const res = await fetch(searchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success && data.items && data.items.length > 0) {
                    renderResults(data.items, data.all_url, query);
                } else {
                    showDropdown();
                    dropdown.innerHTML = `
                        <div style="padding:1.5rem 1rem; text-align:center; font-size:0.875rem; color:#64748b;">
                            Không tìm thấy thiết bị Panasonic nào khớp với "<strong>${escapeHtml(query)}</strong>"
                        </div>
                    `;
                }
            } catch (err) {
                console.error('Lỗi tìm kiếm:', err);
            }
        }

        function handleInput() {
            clearTimeout(debounceTimer);
            const query = input.value.trim();

            if (query.length === 0) {
                hideDropdown();
                dropdown.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                doSearch(query);
            }, 150);
        }

        // Bắt mọi sự kiện gõ chữ, gõ Telex tiếng Việt, paste
        input.addEventListener('input', handleInput);
        input.addEventListener('keyup', handleInput);
        input.addEventListener('paste', () => setTimeout(handleInput, 50));

        // Hiện lại dropdown khi click vào ô tìm kiếm nếu đã có chữ
        input.addEventListener('focus', function () {
            const query = input.value.trim();
            if (query.length > 0) {
                if (dropdown.innerHTML.trim() !== '') {
                    showDropdown();
                } else {
                    doSearch(query);
                }
            }
        });

        // Phím tắt bàn phím '/' để nhảy vào ô search
        window.addEventListener('keydown', (e) => {
            if (e.key === '/' && document.activeElement !== input && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                input.focus();
                input.select();
            }
        });

        // Điều hướng mũi tên lên/xuống trong dropdown
        input.addEventListener('keydown', function (e) {
            const items = dropdown.querySelectorAll('.search-item');
            if (dropdown.hidden || dropdown.style.display === 'none' || !items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                updateFocus(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                updateFocus(items);
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                items[activeIndex].click();
            } else if (e.key === 'Escape') {
                hideDropdown();
            }
        });

        function updateFocus(items) {
            items.forEach((item, idx) => {
                item.classList.toggle('is-focused', idx === activeIndex);
            });
        }

        function renderResults(items, allUrl, query) {
            showDropdown();
            activeIndex = -1;

            const listHtml = items.map(p => `
                <a href="${p.url}" class="search-item">
                    <img src="${p.thumbnail}" alt="${escapeHtml(p.name)}" class="search-item__thumb" width="50" height="50" loading="lazy">
                    <div class="search-item__info">
                        <div class="search-item__name">${highlightMatch(p.name, query)}</div>
                        <div class="search-item__meta">
                            <span class="model-code">${highlightMatch(p.model_code, query)}</span>
                            <span class="search-item__cat">${p.category || ''}</span>
                        </div>
                    </div>
                    <div class="search-item__price">${p.price_text}</div>
                </a>
            `).join('');

            dropdown.innerHTML = `
                <div class="search-results-list">
                    ${listHtml}
                </div>
                <a href="${allUrl}" class="search-all-link">
                    Xem tất cả kết quả cho "<strong>${escapeHtml(query)}</strong>" &rarr;
                </a>
            `;
        }

        // Tự đóng dropdown khi click ra ngoài
        document.addEventListener('click', function (e) {
            if (!form.contains(e.target)) {
                hideDropdown();
            }
        });
    });

    function highlightMatch(text, query) {
        if (!text) return '';
        const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
        return escapeHtml(text).replace(regex, '<mark style="background:#fef08a;color:#854d0e;padding:0 3px;border-radius:2px;font-weight:700;">$1</mark>');
    }

    function escapeHtml(str) {
        return (str || '').replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m]));
    }

    function escapeRegExp(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
})();
