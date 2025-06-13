function addEndpointTypes() {
    const btnEl = document.querySelector('#ed_at_add_type')
    btnEl.addEventListener('click', function () {
        let name = document.querySelector('#ed_at_add_name')
        let slug = document.querySelector('#ed_at_add_slug')
        if (!name || !slug || !isValidSlug(slug.value.trim()) || !checkData([name,slug],'endpoint_type')) {
            alertMessage('Некоректно введені дані для type ендпоінта!', 'error')
        } else {
            let data = {
                'name': name.value.trim(),
                'slug': slug.value.trim()
            }

            callWpAjaxFunction('add_endpoint_type', data)
                .then(response => {
                    name.value = ''
                    slug.value = ''
                    getEndpointTypes(loadEndpointTypes)
                    alertMessage('Тип заявки додано!', 'success')
                })
                .catch(error => {
                    alertMessage('Не вдалося додати тип заявки!', 'error')
                });
        }


    })
}

function getEndpointTypes(func) {
    callWpAjaxFunction('get_endpoint_type')
        .then(response => {
            func(response.data.data)
        })
        .catch(error => {
            alertMessage('Не вдалося отримати типи заявок!', 'error')
        });
}

function loadEndpointTypes(data) {
    try {
        if (!Array.isArray(data)) {
            data = [data]
        }
        let name_data = {
            'name': 'Назва',
            'slug': 'Слаг',
        }
        const ulContainer = document.querySelector('.preview_types_list');
        ulContainer.innerHTML = ''; // очищаємо попередній список

        data.forEach(type => {
            const liContainer = document.createElement('li');
            liContainer.classList.add('preview_types_list_item');

            for (let k in type) {
                const divItem = document.createElement('div');

                const labelItem = document.createElement('span');
                labelItem.textContent = name_data[k];

                const spanItem = document.createElement('span');
                spanItem.textContent = type[k];

                divItem.appendChild(labelItem);
                divItem.appendChild(spanItem);
                liContainer.appendChild(divItem);
            }

            const divAction = document.createElement('div');

            const btnEdit = document.createElement('input');
            btnEdit.type = 'button';
            btnEdit.value = 'Edit';
            btnEdit.addEventListener('click', function () {
                let new_data = {};
                const popUp = document.querySelector('.pop-up-edit-type');
                const nameEdit = document.querySelector('#ed_at_edit_name');
                nameEdit.value = type['name'];
                const slugEdit = document.querySelector('#ed_at_edit_slug');
                slugEdit.value = type['slug'];
                popUp.classList.add('active');

                const btnEditSend = document.querySelector('#ed_at_edit_type');
                btnEditSend.addEventListener('click', function () {
                    if (!nameEdit.value.trim() || !slugEdit.value.trim() || !isValidSlug(slugEdit.value.trim())) {
                        alertMessage('Некоректно введені дані для type ендпоінта!', 'error');
                    } else {
                        new_data['name'] = nameEdit.value.trim();
                        new_data['slug'] = slugEdit.value.trim();
                        callWpAjaxFunction('edit_endpoint_type', [type, new_data])
                            .then(response => {
                                getEndpointTypes(loadEndpointTypes);
                                alertMessage(`Тип заявки ${type['name']} <${type['slug']}> успішно змінено!`, 'success');
                            })
                            .catch(error => {
                                alertMessage(`Не вдалося змінити ${type['name']} <${type['slug']}> тип заявки!`, 'error');
                            });
                        popUp.classList.remove('active');
                    }
                });
            });

            const btnRemove = document.createElement('input');
            btnRemove.type = 'button';
            btnRemove.value = 'X';
            btnRemove.addEventListener('click', function () {
                callWpAjaxFunction('remove_endpoint_type', type)
                    .then(response => {
                        console.log(response);
                        alertMessage(`Тип заявки ${type['name']} <${type['slug']}> успішно видалено!`, 'success');
                        getEndpointTypes(loadEndpointTypes); // Оновлення після видалення
                    })
                    .catch(error => {
                        alertMessage(`Не вдалося видалити ${type['name']} <${type['slug']}> тип заявки!`, 'error');
                    });
            });

            divAction.appendChild(btnEdit);
            divAction.appendChild(btnRemove);
            liContainer.appendChild(divAction);

            ulContainer.appendChild(liContainer);
        });

    } catch (error) {
        console.error('Помилка при завантаженні типів ендпоінтів:', error);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    addEndpointTypes()
    getEndpointTypes(loadEndpointTypes)

})