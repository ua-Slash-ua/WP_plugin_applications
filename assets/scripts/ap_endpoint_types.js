function addEndpointTypes() {
    const btnEl = document.querySelector('#ed_at_add_type')
    btnEl.addEventListener('click', async function () {
        let name = document.querySelector('#ed_at_add_name')
        let slug = document.querySelector('#ed_at_add_slug')
        const isValid = await checkData([name.value.trim(), slug.value.trim()], 'endpoint_type');
        if (!name || !slug || !isValidSlug(slug.value.trim()) || !isValid) {
            alertMessage('Некоректно введені дані для type ендпоінта!', 'error')
            return;
        } else {
            let data = {
                'name': name.value.trim(),
                'slug': slug.value.trim()
            }
            await handleOption('sl_add_option', data, 'endpoint_type')
            await loadEndpointTypes( await handleOption('sl_get_option',[],'endpoint_type'))
            name.value = ''
            slug.value = ''

        }


    })
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
                btnEditSend.addEventListener('click', async function () {
                    if (!nameEdit.value.trim() || !slugEdit.value.trim() || !isValidSlug(slugEdit.value.trim())) {
                        alertMessage('Некоректно введені дані для type ендпоінта!', 'error');
                    } else {
                        new_data['name'] = nameEdit.value.trim();
                        new_data['slug'] = slugEdit.value.trim();
                        await loadEndpointTypes( await handleOption('sl_edit_option', [type, new_data], 'endpoint_type'))
                        await loadEndpointTypes( await handleOption('sl_get_option', [], 'endpoint_type'))

                        popUp.classList.remove('active');
                    }
                });
            });

            const btnRemove = document.createElement('input');
            btnRemove.type = 'button';
            btnRemove.value = 'X';
            btnRemove.addEventListener('click', async function () {
                await handleOption('sl_remove_option', type, 'endpoint_type')
                await loadEndpointTypes( await handleOption('sl_get_option', [], 'endpoint_type'))
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

document.addEventListener("DOMContentLoaded", async function () {
    addEndpointTypes()
    await loadEndpointTypes( await handleOption('sl_get_option',[],'endpoint_type'))

})