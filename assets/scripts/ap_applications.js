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
    function processPopUp(app){
        let previewPriority = {
            'id':"ID заявки: ",
            'name':"Назва типу заявки: ",
            'viewed':"Статус перегляду: ",
            'labels':"Поля заявки: ",
            'created_at':"Дата створення: ",

    }
        const popup = document.querySelector('.pop-up-application');
        const content = document.querySelector('.pop-ap-app-content_list');
        const btnCheckView = document.getElementById('appBtnView');
        content.innerHTML = ''
        document.querySelector('.btn-close').addEventListener('click', ()=>{
            popup.style.display = 'none'
            document.querySelector('.pop-up-overlay').style.display = 'none';
        })
        for (let data_key in previewPriority){
            let data_name = previewPriority[data_key]
            let data_value = app[data_key]
            const liItem = document.createElement('li')
            liItem.classList.add('pop-up-item')
            const itemName = document.createElement('span')
            const itemContent = document.createElement('span')

            itemName.classList.add('pop-up-item-name')
            itemContent.classList.add('pop-up-item-content')
            itemName.textContent = data_name
            liItem.appendChild(itemName)
            if (data_key ==='viewed'){
                itemContent.textContent = data_value === '1' ? 'Переглянуто' : 'Не переглянуто'
                itemContent.classList.add(data_value === '1' ? 'view' : 'not_view')
                btnCheckView.value =  'Позначити як ' + (data_value === '1' ? 'не переглянута' : 'Переглянута')
                btnCheckView.addEventListener('click',async () => {
                    let reverseView = data_value==='1'? '0':'1'
                    let statusView = await handleApplication('sl_set_view',[],app['id'],reverseView)
                    if(statusView){
                        await loadApplications()
                        popup.style.display = 'none'
                        document.querySelector('.pop-up-overlay').style.display = 'none';
                    }


                })
            }else if(data_key ==='labels') {
                app[data_key].forEach(label=>{
                    const divLabel = document.createElement('div')
                    const labelName = document.createElement('span')
                    const labelContent = document.createElement('span')

                    labelName.classList.add('label_name')
                    labelContent.classList.add('label_content')
                    labelName.textContent = getLabelNameBySlug(labelsGlobal,label['meta_key'])
                    labelContent.textContent = label['meta_value']


                    divLabel.appendChild(labelName)
                    divLabel.appendChild(labelContent)

                    itemContent.appendChild(divLabel)
                })
            }else {
                itemContent.textContent = data_value
            }

            liItem.appendChild(itemContent)
            content.appendChild(liItem)
        }




        popup.style.display = 'block'
        document.querySelector('.pop-up-overlay').style.display = 'block';
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
        divActions.classList.add('application_action')
        const btnView = document.createElement('input')
        btnView.classList.add('application_action_view')
        btnView.type = 'button'
        btnView.value = 'View'
        btnView.addEventListener('click', function (){
            processPopUp(app)
        })
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
}
document.addEventListener("DOMContentLoaded", async function () {
    labelsGlobal = await handleOption('sl_get_option', [], 'endpoint_label')
    await loadApplications()
    await loadChooseTypeOrLabel(labelsGlobal,'filtered_ed_label')
    await loadChooseTypeOrLabel(await handleOption('sl_get_option', [], 'endpoint_type'),'filtered_ed_type')
})