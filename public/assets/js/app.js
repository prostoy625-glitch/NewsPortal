// NewsPortal — Practice 11

// 1. Debounce — задержка выполнения функции
function debounce(fn, delay = 300) {
    let timerId;

    return (...args) => {
        clearTimeout(timerId);

        timerId = setTimeout(() => {
            fn(...args);
        }, delay);
    };
}

// 2. Форматирование даты
function formatDate(isoDate) {
    if (!isoDate) {
        return '';
    }

    const [year, month, day] = isoDate.split('-');

    return `${day}.${month}.${year}`;
}

console.log('NewsPortal app.js loaded');

// Проверка формы создания новости
function initNewsForm() {
    const form = document.getElementById('news-create-form');

    if (!form) return;

    const fields = form.querySelectorAll(
        'input[required], textarea[required]'
    );

    function validateField(field) {
        const errorElement = document.getElementById(
            `${field.id}-error`
        );

        let message = '';

        if (!field.value.trim()) {
            message = 'Ин майдон ҳатмист.';
        } else if (!field.checkValidity()) {
            message = 'Маълумоти воридшударо санҷед.';
        }

        field.classList.toggle('is-invalid', message !== '');
        field.setAttribute('aria-invalid', String(message !== ''));

        if (errorElement) {
            errorElement.textContent = message;
        }

        return message === '';
    }

    fields.forEach(field => {
        field.addEventListener('blur', () => {
            validateField(field);
        });

        field.addEventListener('input', () => {
            if (field.classList.contains('is-invalid')) {
                validateField(field);
            }
        });
    });

    form.addEventListener('submit', event => {
        // Пока форма учебная и не сохраняет данные
        event.preventDefault();

        let firstInvalid = null;

        fields.forEach(field => {
            if (!validateField(field) && !firstInvalid) {
                firstInvalid = field;
            }
        });

        if (firstInvalid) {
            firstInvalid.focus();
        } else {
            alert('Форма дуруст пур карда шудааст!');
        }
    });
}

// Подтверждение удаления новости
function initDeleteModal() {
    const modal = document.getElementById('delete-modal');
    const cancelButton = document.getElementById('cancel-delete');
    const confirmButton = document.getElementById('confirm-delete');

    if (!modal || !cancelButton || !confirmButton) {
        return;
    }

    let newsId = null;

    document.querySelectorAll('.delete-news-btn').forEach(button => {
        button.addEventListener('click', () => {
            newsId = button.dataset.id;
            modal.hidden = false;
            cancelButton.focus();
        });
    });

    cancelButton.addEventListener('click', () => {
        modal.hidden = true;
        newsId = null;
    });

    confirmButton.addEventListener('click', () => {
        if (newsId !== null) {
            alert('Удаление новости ID: ' + newsId);
        }

        modal.hidden = true;
        newsId = null;
    });
}

initDeleteModal();
initNewsForm();
// Live Search — поиск новостей
function initNewsSearch() {
    const input = document.getElementById('news-search-input');
    const results = document.getElementById('news-search-results');
    const status = document.getElementById('news-search-status');

    if (!input || !results || !status) {
        return;
    }

    let controller = null;

    const searchNews = debounce(async () => {
        const query = input.value.trim();

        if (controller) {
            controller.abort();
        }

        if (query === '') {
            results.replaceChildren();
            results.hidden = true;
            status.textContent = '';
            return;
        }

        controller = new AbortController();
        status.textContent = 'Ҷустуҷӯ...';

        try {
            const response = await fetch(
                '/NewsPortal/public/api/news/?q=' +
                encodeURIComponent(query),
                { signal: controller.signal }
            );

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const news = await response.json();

            results.replaceChildren();
            results.hidden = false;

            if (news.length === 0) {
                status.textContent = 'Хабар ёфт нашуд.';
                return;
            }

            status.textContent = 'Ёфт шуд: ' + news.length;

            news.forEach(item => {
                const article = document.createElement('article');
                article.className = 'news-card';

                const content = document.createElement('div');
                content.className = 'news-card-content';

                const title = document.createElement('h3');
                title.textContent = item.title;

                const excerpt = document.createElement('p');
                excerpt.textContent = item.excerpt || '';

                const link = document.createElement('a');
                link.className = 'read-more';
                link.href = '/NewsPortal/news/' +
                    encodeURIComponent(item.slug);
                link.textContent = 'Муфассалтар →';

                content.append(title, excerpt, link);
                article.appendChild(content);
                results.appendChild(article);
            });

        } catch (error) {
            if (error.name === 'AbortError') return;

            results.replaceChildren();
            results.hidden = true;
            status.textContent = 'Хатогӣ ҳангоми ҷустуҷӯ.';
            console.error(error);
        }
    }, 300);

    input.addEventListener('input', () => {
        if (controller) {
            controller.abort();
        }

        searchNews();
    });
}

initNewsSearch();