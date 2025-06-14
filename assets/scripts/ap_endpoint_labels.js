function listenLabelType() {
    const select = document.getElementById('choose_label_type');


    select.addEventListener('change', function () {
        const selectedValue = this.value;
        console.log("Обрано:", selectedValue);

        if (selectedValue === 'l_text') {
            document.querySelector('.l_file_container').style.display = 'none'

            document.querySelector('.l_text_container').style.display = 'block'
        } else if (selectedValue === 'l_file') {

            document.querySelector('.l_text_container').style.display = 'none'

            document.querySelector('.l_file_container').style.display = 'block'
        }
    });
}

async function loadLabels(data) {
    let dataCompliance = {
        'name':'Назва поля',
        'slug':'Слаг поля',
        'type':'Тип поля',
        'file_types':'Типи файлу(ів)',
        'file_size':'Розмір файлу(ів)',
    }
    function loadLabelText(data){
        const labelItem = document.createElement('li')

        for(let key in data){
            const elContainer = document.createElement('div')
            const nameLabel = document.createElement('span')
            nameLabel.textContent = dataCompliance[key]
            const valueLabel = document.createElement('span')
            valueLabel.textContent = data[key]
            elContainer.appendChild(nameLabel)
            elContainer.appendChild(valueLabel)
            labelItem.appendChild(elContainer)
        }
        const elContainerAction = document.createElement('div')
        const btnRemove = document.createElement('input')
        btnRemove.type = 'button'
        btnRemove.value = 'Delete'
        const btnEdit = document.createElement('input')
        btnEdit.type = 'button'
        btnEdit.value = 'Edit'

        elContainerAction.appendChild(btnEdit)
        elContainerAction.appendChild(btnRemove)
        labelItem.appendChild(elContainerAction)

        return labelItem
    }
    function loadLabelFile(data) {
        console.log(data)
        const labelItem = document.createElement('li');

        for (let key in data) {
            const elContainer = document.createElement('div');
            const nameLabel = document.createElement('span');

            // Якщо є dataCompliance, використовуй, інакше — key як текст
            nameLabel.textContent = dataCompliance[key]

            let valueLabel; // оголошуємо один раз

            if (key === 'file_types') {
                valueLabel = document.createElement('ul');
                data[key].forEach(fType => {
                    const liLabel = document.createElement('li');
                    liLabel.textContent = fType.replace('l_file_extend_', '.');
                    liLabel.setAttribute('file-types', fType);
                    valueLabel.appendChild(liLabel);
                });
            } else {
                valueLabel = document.createElement('span');
                valueLabel.textContent = data[key];
            }

            elContainer.appendChild(nameLabel);
            elContainer.appendChild(valueLabel);
            labelItem.appendChild(elContainer);
        }

        const elContainerAction = document.createElement('div');
        const btnRemove = document.createElement('input');
        btnRemove.type = 'button';
        btnRemove.value = 'Delete';

        const btnEdit = document.createElement('input');
        btnEdit.type = 'button';
        btnEdit.value = 'Edit';

        elContainerAction.appendChild(btnEdit);
        elContainerAction.appendChild(btnRemove);
        labelItem.appendChild(elContainerAction);

        return labelItem;
    }



    if (!Array.isArray(data)) {
        data = [data]
    }
    const labelsContainer = document.querySelector('.labels_container')
    data.forEach( label =>{
        if (label['type'] ==='l_text') {
            labelsContainer.appendChild(loadLabelText(label))
        }else if(label['type'] ==='l_file'){
            labelsContainer.appendChild(loadLabelFile(label))
        }
    })

}

function addLabel() {
    function getLabelText() {
        let data = {}
        const lName = document.getElementById('l_text_name')
        const lSlug = document.getElementById('l_text_slug')
        if (!lName.value.trim() || !lSlug.value.trim() || !isValidSlug(lSlug.value.trim())) {
            alertMessage('Невірно заповнені дані', 'error')
            return
        }

        data['name'] = lName.value
        data['slug'] = lSlug.value

        lName.value = ''
        lSlug.value = ''

        return data
    }

    function getLabelFile() {
        let data = {}
        const lName = document.getElementById('l_file_name')
        const lSlug = document.getElementById('l_file_slug')
        const fileTypeSelect = document.getElementById('choose_file_type');
        const fileSizeSelect = document.getElementById('file_size_range');


        const selectedValues = Array.from(fileTypeSelect.selectedOptions).map(option => option.value);
        if (!lName.value.trim()
            || !lSlug.value.trim()
            || fileSizeSelect.value === '0'
            || selectedValues.length === 0
            || !isValidSlug(lSlug.value.trim()))
        {
            alertMessage('Невірно заповнені дані', 'error')
            return
        }

        data['name'] = lName.value
        data['slug'] = lSlug.value
        data['file_types'] = selectedValues
        data['file_max_size'] = fileSizeSelect.value

        lName.value = ''
        lSlug.value = ''
        fileSizeSelect.value = '1'
        document.getElementById('file_size_value').textContent =
            fileSizeSelect.value ? fileSizeSelect.value + ' МБ' : '0 МБ';        // Скинути всі вибрані опції
        for (let option of fileTypeSelect.options) {
            option.selected = false;
        }
        return data
    }

    const btnAdd = document.getElementById('l_action_add');
    btnAdd.addEventListener('click', async function () {
        let data = {}
        const select = document.getElementById('choose_label_type');
        if (select.value === 'l_text') {
            data = getLabelText()
        } else {
            data = getLabelFile()
        }
        if (!data) {
            return
        }
        data['type'] = select.value
        await loadLabels(data)
    });
}

document.addEventListener("DOMContentLoaded", function () {
    listenLabelType()
    addLabel()
})