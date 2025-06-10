
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
        console.log('-12312')
        data['login'] = 'sl_l' + document.getElementById('ba_add_login').value
        data['pass'] = 'sl_p' + document.getElementById('ba_add_password').value
        callWpAjaxFunction('add_log_and_pass', data)
            .then(response => console.log('Success:', response.data))
            .catch(error => console.error('Error:', error));
    })


}
// 123 sd a

document.addEventListener("DOMContentLoaded", function () {
    let tabs = ['auto_fill_data', 'security_data']
    actionTab(tabs)
    generateLogAndPass()
    addLogAndPass()
    alertMessage('Красава','info')
})
