let dataCompliance = {
    'name': 'Назва:',
    'path_end': 'Кінцевий шлях:',
    'type': 'Тип(категорія):',
    'method': 'Метод:',
    'labels': 'Поля:',
    'label_type_text': 'Текстове поле ',
    'label_type_file': 'Файлове поле ',
    'label_slug': 'Поля:',
}

async function prepareOption(data) {
    let newData = []
    data.forEach(el => {
        newData.push(JSON.parse(el['options_value']))
    })
    return newData
}

async function loadChooseTypeOrLabel(data, selectId) {
    if (!Array.isArray(data)) {
        data = [data]
    }
    // console.log(data)
    const selectType = document.querySelector(`#${selectId}`)
    data.forEach(type => {
        const optionType = document.createElement('option')
        optionType.value = type.slug
        optionType.textContent = type.name
        selectType.appendChild(optionType)
    })
}

function addEndpoint() {
    async function getEndpointData() {
        let data = {}
        const edName = document.getElementById('ep_add_text_name')
        const edPathEnd = document.getElementById('ep_add_path_end')
        const edType = document.getElementById('ep_add_type')
        const edLabels = document.getElementById('ep_add_labels')
        const edMethod = document.getElementById('ep_add_method')

        let name = edName.value.trim()
        let pathEnd = edPathEnd.value.trim()
        let method = edMethod.value.trim()


        let typeSlug = edType.value.trim()
        let labels = Array.from(edLabels.selectedOptions).map(option => option.value);


        data['name'] = name
        data['path_end'] = pathEnd
        data['method'] = method
        data['type'] = await prepareOption(await checkData([typeSlug], 'endpoint_type', true, ['slug']))
        data['labels'] = await prepareOption(await checkData(labels, 'endpoint_label', true, ['slug']))
        console.log(data)
        return data
    }

    const selectType = document.getElementById('ep_endpoint_btn_add')
    selectType.addEventListener('click', async function () {
        let data = await getEndpointData()
        data = await handleOption('sl_add_option', data, 'endpoints')
        await loadEndpoints(data)
    })
}

async function loadEndpoints(data) {
    if (!Array.isArray(data)) {
        data = [data]
    }
    const mainContainer = document.querySelector('.ep_review_container').querySelector('ul')
    mainContainer.innerHTML = ''
    data.forEach(endpoint => {
        const liContainer = document.createElement('li')
        // Endpoint NAME
        const nameContainer = document.createElement('div')
        const nameLabel = document.createElement('span')
        nameLabel.textContent = dataCompliance['name']
        const nameContent = document.createElement('span')
        nameContent.textContent = endpoint['name']

        nameContainer.appendChild(nameLabel)
        nameContainer.appendChild(nameContent)

        // Endpoint TYPE
        const typeContainer = document.createElement('div')
        const typeLabel = document.createElement('span')
        typeLabel.textContent = dataCompliance['type']
        const typeContent = document.createElement('span')
        typeContent.textContent = endpoint['type'][0]['name']

        typeContainer.appendChild(typeLabel)
        typeContainer.appendChild(typeContent)

        // Endpoint METHOD
        const methodContainer = document.createElement('div')
        const methodLabel = document.createElement('span')
        methodLabel.textContent = dataCompliance['method']
        const methodContent = document.createElement('span')
        methodContent.textContent = endpoint['method'].replace('ep_add_method_', '').toUpperCase()

        methodContainer.appendChild(methodLabel)
        methodContainer.appendChild(methodContent)

        // Endpoint END PATH
        const endPathContainer = document.createElement('div')
        const endPathLabel = document.createElement('span')
        endPathLabel.textContent = dataCompliance['path_end']
        const endPathContent = document.createElement('span')
        endPathContent.textContent = 'applications/v1' + endpoint['path_end']

        endPathContainer.appendChild(endPathLabel)
        endPathContainer.appendChild(endPathContent)

        // Endpoint LABEL
        const labelMainContainer = document.createElement('ul')


        endpoint['labels'].forEach(label => {
            const labelContainer = document.createElement('li')
            labelContainer.classList.add('label_item')
            labelContainer.innerHTML = dataCompliance[label['type'] === 'l_text'? 'label_type_text': 'label_type_file'] +
                    `<span class="label_name"> ${label.name} </span>  з ` +
                `<span class="label_slug"> (${label.slug}) </span>`

                labelMainContainer.appendChild(labelContainer)
        })
        // Endpoint ACTION
        const actionContainer = document.createElement('div')
        const btnRemove = document.createElement('input')
        btnRemove.type = 'button'
        btnRemove.value = 'Remove'

        const btnEdit = document.createElement('input')
        btnEdit.type = 'button'
        btnEdit.value = 'Edit'

        actionContainer.appendChild(btnEdit)
        actionContainer.appendChild(btnRemove)

        liContainer.appendChild(nameContainer)
        liContainer.appendChild(typeContainer)
        liContainer.appendChild(methodContent)
        liContainer.appendChild(endPathContainer)
        liContainer.appendChild(labelMainContainer)
        liContainer.appendChild(actionContainer)

        mainContainer.appendChild(liContainer)




    })

}

document.addEventListener("DOMContentLoaded", async function () {
    await loadChooseTypeOrLabel(await handleOption('sl_get_option', [], 'endpoint_label'), 'ep_add_labels')
    await loadChooseTypeOrLabel(await handleOption('sl_get_option', [], 'endpoint_type'), 'ep_add_type')
    addEndpoint()
    await loadEndpoints(await handleOption('sl_get_option', [], 'endpoints'))

})