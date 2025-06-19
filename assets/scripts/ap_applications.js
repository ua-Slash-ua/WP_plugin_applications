let labelsGlobal = []
async function previewApplications(data){
    function createMainLabelDiv(labels, requestedKey = null) {
        const priorityKeys = ['email', 'phone', 'full_name', 'name'];

        // Створюємо мапу для зручного доступу до значень за ключем
        const labelMap = {};
        labels.forEach(label => {
            labelMap[label.meta_key] = label.meta_value;
        });

        // Визначаємо ключ, який будемо використовувати
        let displayKey = null;

        if (requestedKey && labelMap[requestedKey]) {
            displayKey = requestedKey;
        } else {
            displayKey = priorityKeys.find(key => labelMap[key]) || labels[0]?.meta_key;
        }

        // Створюємо HTML елементи
        const divLabel = document.createElement('div');
        divLabel.classList.add('application_label_container')
        const spanLabelName = document.createElement('span');
        spanLabelName.classList.add('application_label_name')
        const spanLabelContent = document.createElement('span');
        spanLabelContent.classList.add('application_label_content')
        spanLabelName.textContent = getLabelNameBySlug(labelsGlobal,displayKey);
        spanLabelContent.textContent = labelMap[displayKey] || '—';

        divLabel.appendChild(spanLabelName);
        divLabel.appendChild(spanLabelContent);

        return divLabel;
    }

    if (!Array.isArray(data)){
        data = [data]
    }
    const previewContainer = document.querySelector('.preview_container_list')
    previewContainer.innerHTML = ''
    data.forEach(app=>{
        const liContainer = document.createElement('li')

        // Create ID
        const spanID = document.createElement('span')
        spanID.classList.add('application_id')
        spanID.textContent =app['id']

        // Create TYPE_NAME
        const spanName = document.createElement('span')
        spanName.classList.add('application_name')
        spanName.textContent =app['name']

        // Create LABELS
        const divLabel = createMainLabelDiv(app['labels'])


        // Create VIEWED
        const spanView = document.createElement('span')
        if (app['viewed'] === '1'){
            spanView.classList.add('application_view')
            spanView.textContent = 'Переглянуто'
        }else{
            spanView.classList.add('application_not_view')
            spanView.textContent = 'Не переглянуто'
        }

        // Create TIME_CREATED
        const spanTime = document.createElement('span')
        spanTime.classList.add('application_time')
        spanTime.textContent =app['created_at']

        // Create ACTIONS
        const divActions = document.createElement('div')
        const btnView = document.createElement('input')
        btnView.classList.add('application_action_view')
        btnView.type = 'button'
        btnView.value = 'View'
        const btnRemove = document.createElement('input')
        btnRemove.classList.add('application_action_remove')
        btnRemove.type = 'button'
        btnRemove.value = 'Remove'

        divActions.appendChild(btnView)
        divActions.appendChild(btnRemove)


        liContainer.appendChild(spanID)
        liContainer.appendChild(spanName)
        liContainer.appendChild(divLabel)
        liContainer.appendChild(spanView)
        liContainer.appendChild(spanTime)
        liContainer.appendChild(divActions)
        previewContainer.appendChild(liContainer)

    })
}

async function loadApplications() {
    await previewApplications(await handleApplication('sl_get_applications',[]))
    console.log(await handleApplication('sl_get_applications',[]))

}
document.addEventListener("DOMContentLoaded", async function () {
    labelsGlobal = await handleOption('sl_get_option', [], 'endpoint_label')
    await loadApplications()
    await loadChooseTypeOrLabel(labelsGlobal,'filtered_ed_label')
    await loadChooseTypeOrLabel(await handleOption('sl_get_option', [], 'endpoint_type'),'filtered_ed_type')
})