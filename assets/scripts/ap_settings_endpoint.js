function isValidSlug(slug,symbol = /^[a-zA-Z0-9_-]+$/) {
    return symbol.test(slug);
}

function showVisibleElement(btnClass, containerClass) {
    const btnEl = document.querySelector(`.${btnClass}`)
    const container = document.querySelector(`.${containerClass}`)
    if (container) {
        btnEl.addEventListener('click', function () {
            if (container.style.display !== 'none') {
                container.style.display = 'none'
                btnEl.innerHTML = 'Показати'
            } else {
                container.style.display = 'flex'
                btnEl.innerHTML = 'Приховати'
            }
        })

    }


}

function addEndpointTypes() {
    const btnEl = document.querySelector('#ed_at_add_type')
    btnEl.addEventListener('click', function () {
        let name = document.querySelector('#ed_at_add_name')
        let slug = document.querySelector('#ed_at_add_slug')
        if (!name || !slug || !isValidSlug(slug.value.trim())) {
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

function loadEndpointTypesSelect(data) {
    try {
        if (!Array.isArray(data)) {
            data = [data]
        }
        let name_data = {
            'name': 'Назва',
            'slug': 'Слаг',
        }
        const select = document.getElementById('choose_ec_label_type')
        select.innerHTML = ''
        data.forEach(type => {
            const option = document.createElement('option')
            option.value = type['slug']
            option.textContent = type['name']
            select.appendChild(option)
        })


    } catch (e) {
        console.error(e)
    }
}

function addLabel() {
    const btnAdd = document.getElementById('ec_label_add')
    btnAdd.addEventListener('click', function () {
        const labelNameItem = document.getElementById('ec_label_name_label')
        const labelTypeItem = document.getElementById('choose_ec_label_create')
        const labelMandatItem = document.getElementById('ec_label_mandatory_label')

        if (!labelNameItem.value.trim() || !isValidSlug(labelNameItem.value.trim(),/^[a-zA-Z0-9_]+$/)){
            alertMessage('Невірно вказані дані для поля!','error')
            return
        }

        const ulLabelContainer = document.querySelector('.ec_label_preview').querySelector('ul')
        const liItem = document.createElement('li')

        const liItemName = document.createElement('span')
        liItemName.textContent = labelNameItem.value.trim()
        const liItemType = document.createElement('span')
        liItemType.textContent = labelTypeItem.value
        const liItemMandat = document.createElement('span')
        liItemMandat.textContent = labelMandatItem.checked ? 'on' : 'off';

        const btnRemove = document.createElement('input')
        btnRemove.type = 'input'
        btnRemove.value = 'X'
        liItemMandat.textContent = labelMandatItem.checked ? 'on' : 'off';

        liItem.appendChild(liItemName)
        liItem.appendChild(liItemType)
        liItem.appendChild(liItemMandat)
        liItem.appendChild(btnRemove)

        ulLabelContainer.appendChild(liItem)

        labelNameItem.value = ''
        labelMandatItem.checked = false

            })
}

function addEndpoint() {
    const btnAdd = document.getElementById('ec_endpoint_add')
    btnAdd.addEventListener('click', function () {
        let endpoint = {

        }
        let labels = []
        const name = document.getElementById('ec_end_name')
        endpoint['name'] = name.value
        const basePath = document.getElementById('choose_way_directory')
        endpoint['base_path'] = basePath.value
        const endPath = document.getElementById('input_way_end_directory')
        endpoint['end_path'] = endPath.value
        const endTypes = document.getElementById('choose_ec_label_type')
        endpoint['type'] = endTypes.value
        const labelsContainer = [...document.querySelector('.ec_label_preview').querySelectorAll('li')]
        labelsContainer.forEach( liItem=>{
            let dataLabel = {}
            dataLabel['name'] = liItem.children[0].textContent
            dataLabel['type'] = liItem.children[1].textContent
            dataLabel['isMandat'] = liItem.children[2].textContent
            labels.push(dataLabel)
        })
        endpoint['labels'] = labels
        console.log('----')
        console.log(endpoint)
        console.log('----')
        callWpAjaxFunction('add_endpoint', endpoint)
            .then(response => {
                loadEndpoint()
                alertMessage(`Ендпоінт ${endpoint['name']} додано!!`, 'success');
            })
            .catch(error => {
                alertMessage(`Не вдалося додати ендпоінт ${endpoint['name']} !`, 'error');
            });
    })
}

function loadEndpoint(){
    function displayEndpoints(endpoints) {
        const endpointList = document.querySelector('.endpoint_list');
        endpointList.innerHTML = ''; // Очистка перед додаванням нових елементів
        if (!Array.isArray(endpoints)){
            endpoints = [endpoints]
        }
        endpoints.forEach(endpoint => {
            const liItem = document.createElement('li');
            liItem.classList.add('endpoint_item');

            // Назва ендпоінта
            const nameSpan = document.createElement('h4');
            nameSpan.textContent = endpoint.name;
            liItem.appendChild(nameSpan);

            // Шлях ендпоінта
            const pathSpan = document.createElement('p');
            pathSpan.innerHTML = `<strong>Шлях:</strong> ${endpoint.base_path}/${endpoint.end_path}`;
            liItem.appendChild(pathSpan);

            // Тип ендпоінта
            const typeSpan = document.createElement('p');
            typeSpan.innerHTML = `<strong>Тип:</strong> ${endpoint.type}`;
            liItem.appendChild(typeSpan);

            // Поля ендпоінта
            if (endpoint.labels && endpoint.labels.length > 0) {
                const labelsList = document.createElement('ul');
                labelsList.classList.add('labels_list');

                endpoint.labels.forEach(label => {
                    const labelItem = document.createElement('li');
                    labelItem.innerHTML = `<span>${label.name}</span> <span class="label-type">${label.type}</span> <span class="mandatory">${label.isMandat === 'on' ? 'Обов’язкове' : 'Не обов’язкове'}</span>`;
                    labelsList.appendChild(labelItem);
                });

                liItem.appendChild(labelsList);
            }

            endpointList.appendChild(liItem);
        });
    }
    callWpAjaxFunction('get_endpoint')
        .then(response => {
            console.log(response.data.data)
            displayEndpoints(response.data.data)
        })
        .catch(error => {
            alertMessage(`Не вдалося отримати ендпоінти!`, 'error');
        });
}
document.addEventListener("DOMContentLoaded", function () {

    showVisibleElement('show-visible', 'short_description_content')
    addEndpointTypes()
    getEndpointTypes(loadEndpointTypes)
    getEndpointTypes(loadEndpointTypesSelect)
    addLabel()
    addEndpoint()
    loadEndpoint()
})