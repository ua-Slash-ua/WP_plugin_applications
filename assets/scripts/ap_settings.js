
function actionTab(tabs) {
    tabs.forEach(tabName => {
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


function generateSecure(length = 32) {
    const chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let result = '';

    // Використовуємо crypto.getRandomValues для кращої випадковості
    const randomValues = new Uint8Array(length);
    crypto.getRandomValues(randomValues);

    for (let i = 0; i < length; i++) {
        result += chars[randomValues[i] % chars.length];
    }

    return result;
}

function generateLogAndPass(){
    let idInput = ['ba_add_login','ba_add_password']
    const btnGenerate = document.querySelector(`#ba_add_generate`)
    btnGenerate.addEventListener('click', function (){
        idInput.forEach(elId =>{
            document.getElementById(elId).value = generateSecure(28)
        })
    })
}

function addLogAndPass(){
    let data = {}
    const btnAdd = document.querySelector(`#ba_add_add`)
    btnAdd.addEventListener('click', function (){
        data['login'] = 'sl_l' + document.getElementById('ba_add_login').value
        data['pass'] = 'sl_p' + document.getElementById('ba_add_password').value
        callWpAjaxFunction('add_log_and_pass', data)
            .then(response => {
                loadLogAndPass(response.data.data)
                alertMessage('Дані для аутентифікації додано!', 'success')
            })
            .catch(error => {
                alertMessage('Дані для аутентифікації не вдалося додати!', 'error')
            });
    })


}

function loadLogAndPass(data){
    function loadContainer(data){
        const previewContainer = document.querySelector('.base_auth_preview')
        const pcContent = previewContainer.querySelector('ul')
        pcContent.innerHTML = ''
        data.forEach( logAndPass =>{
            const liItem = document.createElement('li')

            const spanLog = document.createElement('span')
            spanLog.textContent = logAndPass['login']
            const spanPass = document.createElement('span')
            spanPass.textContent = logAndPass['pass']
            const inputRemove = document.createElement('input')
            inputRemove.type = 'button'
            inputRemove.value = 'X'
            inputRemove.addEventListener('click', function (){

                callWpAjaxFunction('remove_log_and_pass', logAndPass)
                    .then(response => {
                        alertMessage('Дані для аутентифікації видалені', 'success')
                        this.parentElement.remove()
                    })
                    .catch(error => {
                        alertMessage('Не вдалося видалити дані для аутентифікації!', 'error')
                    });

            })
            liItem.appendChild(spanLog)
            liItem.appendChild(spanPass)
            liItem.appendChild(inputRemove)
            pcContent.appendChild(liItem)
        })
        previewContainer.appendChild(pcContent)

    }
    if (!data){
        callWpAjaxFunction('get_log_and_pass', [])
            .then(response => {
                loadContainer(response.data.data)
            })
            .catch(error => {
                alertMessage('Не вдалося отримати дані для аутентифікації!', 'error')
            });
    }else {
        loadContainer(data)
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let tabs = ['auto_fill_data', 'security_data']
    actionTab(tabs)
    generateLogAndPass()
    addLogAndPass()
    loadLogAndPass()
})
