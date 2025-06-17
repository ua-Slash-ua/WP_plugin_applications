jQuery(document).ready(function ($) {
    // Мапа типів заявки у читабельний текст
    const typeLabels = {
        'nanny_match': 'Підбір няні',
        'training_application': 'Навчання',
        'nanny_signup': 'Заявка від няні',
        'nanny_review': 'Відгук на вакансію',
        // додайте інші типи за потреби
    };

    // Мапа для виводу полів заявки у модалці
    const fieldLabels = {
        id: 'ID заявки',
        type: 'Тип заявки',
        is_viewed: 'Статус перегляду',
        full_name: 'Повне ім’я',
        phone: 'Телефон',
        email: 'Електронна пошта',
        location: 'Локація',
        format: 'Формат',
        vacancy: 'Назва вакансії', // ← нове поле
        created_at: 'Дата створення',
    };

    const emojiMap = {
        id: '🆔',
        type: '📋',
        is_viewed: '👁️',
        full_name: '👤',
        phone: '📞',
        email: '📧',
        location: '📍',
        format: '📝',
        vacancy: '💼', // ← іконка для vacancy
        created_at: '📅',
    };


    function fetchApplications(filters = {}) {
        console.group('fetchApplications');
        console.log('Запит заявок з фільтрами:', filters);

        $.ajax({
            url: applicationsData.ajax_url,
            method: 'POST',
            data: {
                action: 'get_applications',
                nonce: applicationsData.nonce,
                filters: filters
            },
            success: function (response) {
                console.log('Відповідь сервера:', response);

                if (response.success) {
                    renderApplications(response.data.items);
                    console.log(`Відображено заявок: ${response.data.items.length} з ${response.data.total}`);
                } else {
                    $('#applications-list').html('<p>Помилка: ' + (response.data || 'Неочікувана відповідь') + '</p>');
                    console.warn('Помилка у відповіді сервера:', response.data);
                }

                console.groupEnd();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#applications-list').html('<p>Помилка при завантаженні заявок</p>');
                console.error('AJAX помилка:', textStatus, errorThrown);
                console.groupEnd();
            }
        });
    }

    function renderApplications(applications) {
        console.group('renderApplications');
        console.log('Масив заявок для відображення:', applications);

        const $list = $('#applications-list');
        $list.empty();

        if (!applications.length) {
            $list.append('<p>Заявок не знайдено</p>');
            console.log('Заявок не знайдено.');
            console.groupEnd();
            return;
        }

        applications.forEach(app => {
            const typeText = typeLabels[app.type] || app.type;
            const status = app.is_viewed == 1 ? '✅ Переглянута' : '🕒 Не переглянута';

            const row = $(`
                <div class="application-item" data-id="${app.id}">
                    <strong>#${app.id}</strong>
                    <div class="type">${typeText}</div>
                    <div class="name">${app.full_name}</div>
                    <button class="view-application button">Переглянути</button>
                    <div class="status">${status}</div>
                </div>
            `);

            $list.append(row);
        });

        console.groupEnd();
    }

    // Відкриття модалки з деталями заявки
    $('#applications-list').on('click', '.view-application', function () {
        const id = $(this).closest('.application-item').data('id');
        console.log('Запит деталей заявки, ID:', id);

        $.ajax({
            url: applicationsData.ajax_url,
            method: 'POST',
            data: {
                action: 'get_application',
                nonce: applicationsData.nonce,
                id: id
            },
            success: function (response) {
                if (!response.success) {
                    alert('Заявку не знайдено');
                    return;
                }

                const app = response.data;
                const $details = $('#application-details');
                $details.empty();

                for (const [key, value] of Object.entries(app)) {
                    let displayValue = value;

                    // Пропустити порожні значення
                    if (
                        displayValue === null ||
                        displayValue === undefined ||
                        (typeof displayValue === 'string' && displayValue.trim() === '') ||
                        (Array.isArray(displayValue) && displayValue.length === 0)
                    ) {
                        continue;
                    }

                    if (key === 'type') {
                        displayValue = typeLabels[value] || value;
                    }

                    if (key === 'is_viewed') {
                        displayValue = (value == 1 || value === '1') ? 'Переглянуто' : 'Не переглянуто';
                    }

                    if (key === 'format' && typeof value === 'string') {
                        const arr = value.split(',').map(s => s.trim()).filter(Boolean);
                        displayValue = arr.join(', ');
                    }

                    if (Array.isArray(displayValue)) {
                        displayValue = displayValue.join(', ');
                    }

                    const label = fieldLabels[key] || key.replace(/_/g, ' ');
                    const emoji = emojiMap[key] || 'ℹ️';

                    $details.append(`
                    <p><span class="emoji">${emoji}</span> <strong>${label}:</strong> ${displayValue}</p>
                `);
                }

                // Відкрити модалку
                $('#application-modal').data('id', app.id).removeClass('hidden');
                $('#mark-viewed').data('id', app.id);
            },
            error: function () {
                alert('Помилка при завантаженні деталей заявки.');
            }
        });
    });


    // Закриття модального вікна
    $('#close-modal').on('click', function () {
        console.log('Закриття модального вікна заявки');
        $('#application-modal').addClass('hidden');
    });

    // Позначити заявку як переглянуту
    $('#mark-viewed').on('click', function () {
        const id = $(this).data('id');
        console.log('Позначити заявку як переглянуту, ID:', id);

        $.ajax({
            url: applicationsData.ajax_url,
            method: 'POST',
            data: {
                action: 'mark_application_viewed',
                nonce: applicationsData.nonce,
                id: id
            },
            success: function () {
                console.log('Статус заявки оновлено');
                $('#application-modal').addClass('hidden');
                fetchApplications(); // оновити список
            },
            error: function () {
                alert('Не вдалося оновити статус заявки.');
            }
        });
    });

    // Обробка фільтрів
    $('#applications-filter-form').on('submit', function (e) {
        e.preventDefault();
        const filters = $(this).serializeArray().reduce((obj, item) => {
            if (item.value) obj[item.name] = item.value;
            return obj;
        }, {});
        console.log('Фільтри форми застосовано:', filters);
        fetchApplications(filters);
    });

    // Скидання фільтрів
    $('#reset-filters').on('click', function () {
        $('#applications-filter-form')[0].reset();
        console.log('Фільтри скинуто');
        fetchApplications({});
    });

    // Початкове завантаження заявок
    console.log('Початкове завантаження заявок');
    fetchApplications({});
});
