function alertMessage(msg, sts = 'info', time = 5) {
    let status = {
        "success": {
            'class': 'alert-status-success',
            'svgIcon': `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="green" viewBox="0 0 24 24">
                    <path d="M9 17l-5-5 1.41-1.41L9 14.17l9.59-9.59L20 6l-11 11z"/>
                </svg>`
        },
        "info": {
            'class': 'alert-status-info',
            'svgIcon': `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="blue" viewBox="0 0 24 24">
                 <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
             </svg>`
        },
        "error": {
            'class': 'alert-status-error',
            'svgIcon': `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="red" viewBox="0 0 24 24">
                 <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
             </svg>`
        },

    }
    let svgIcon = status[sts]['svgIcon']
    let cls = status[sts]['class']
    function showAlert(f_msg, f_sts, svgIcon, cls) {
        const alertContainer = document.querySelector('.message_alert');
        const alertContainerIcon = document.querySelector('.msg-icon');
        const alertContainerText = document.querySelector('p');
        const alertContainerH3 = document.querySelector('h3');
        const progressBar = document.querySelector('.alert-progress-bar');

        alertContainerIcon.innerHTML = svgIcon;
        alertContainerText.textContent = f_msg;
        alertContainerH3.textContent = `Application повідомляє < ${f_sts} >`;
        alertContainer.classList.add(cls);

        // Початкове значення прогрес-бару
        progressBar.style.width = '100%';
        progressBar.style.transition = `width ${time}s linear`;

        // Запускаємо плавне заповнення
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 100); // Коротка затримка, щоб `transition` коректно відпрацював

        setTimeout(() => {
            Object.keys(status).forEach(key => {
                let s = status[key]['class'];
                if (alertContainer.classList.contains(s)) {
                    alertContainer.classList.remove(s);
                }
            });

            // Після завершення повзунок плавно зменшується назад до 0%
            progressBar.style.width = '0%';

        }, time *1075);
    }




    showAlert(msg,sts,svgIcon,cls)

}
function actionTab(tabs) {
    tabs.forEach(tabName => {
        if (!document.getElementById(tabName)){
            return;
        }
        document.getElementById(tabName).addEventListener('click', function () {
            document.querySelectorAll('.mtab_header_item').forEach(navEl => {
                navEl.classList.remove('tab_active')
            })
            document.querySelectorAll('.mtab_content_item').forEach(navEl => {
                navEl.classList.remove('content_active')
            })
            document.getElementById(tabName).classList.add('tab_active')
            document.getElementById(`content_${tabName}`).classList.add('content_active')
        })
    })
}
function isValidSlug(slug,symbol = /^[a-zA-Z0-9_-]+$/) {
    return symbol.test(slug);
}

async function checkData(data, key = null, returnData = false,keyArray = []) {
    if (!Array.isArray(data)) {
        return false;
    }

    const dataSend = {
        key: key,
        value: data,
        keysArray: keyArray,
    };
    try {

        const response = await callWpAjaxFunction('finder_options', dataSend);
        if (!returnData){
            return response.success === true;
        }else {
            return response.data.data
        }

    } catch (error) {
        alertMessage(`Такі дані для ${key} уже існують`,'error')
        return false;

    }
}
async function handleOption(func, data, key, opId = null) {
    // console.log(func)
    // console.log(data)
    // console.log(key)
    if (!Array.isArray(data) && typeof data !== 'object') { // підтримати об'єкт і масив
        return false;
    }

    const dataSend = {
        key: key,
        value: data,
        opId: opId,
    };

    try {

        const response = await callWpAjaxFunction(func, dataSend);
        if (!func.startsWith('sl_get_')){
            alertMessage(response.data.message, "success");
        }
        // console.log(response)
        return response.data.data || [];  // повернути результат або true якщо немає data

    }  catch (error) {
        alertMessage(error.message || "Невідома помилка", "error");
        console.error("handleOption Error:", error);
        return null;
    }
}

async function handleApplication(func, data, id = 0 , view = '0'){
    if (!Array.isArray(data) && typeof data !== 'object') { // підтримати об'єкт і масив
        return false;
    }
    try {
        const dataSend = {
            value: data,
            id: id,
            view: view,
        };

        const response = await callWpAjaxFunction(func, dataSend);
        if (!func.startsWith('sl_get_')){
            alertMessage(response.data.message, "success");
        }
        // console.log(response)
        return response.data.data || [];  // повернути результат або true якщо немає data

    }  catch (error) {
        alertMessage(error.message || "Невідома помилка", "error");
        console.error("handleApplication Error:", error);
        return null;
    }
}

async function loadChooseTypeOrLabel(data, selectId) {
    if (!Array.isArray(data)) {
        data = [data]
    }
    const selectType = document.querySelector(`#${selectId}`)
    data.forEach(type => {
        const optionType = document.createElement('option')
        optionType.value = type.slug
        optionType.textContent = type.name
        selectType.appendChild(optionType)
    })
}
function getLabelNameBySlug(labels, slug) {
    const found = labels.find(label => label.slug === slug);
    return found ? found.name : null;
}

document.addEventListener("DOMContentLoaded", function () {

    let tabs = [
        'main_data',
        'endpoint_data',
        'security_data',
        'endpoint_main',
        'endpoint_label',
        'endpoint_type',
    ]
    actionTab(tabs)
})