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

function generateLogAndPass() {
    let idInput = ['ba_add_login', 'ba_add_password']
    const btnGenerate = document.querySelector(`#ba_add_generate`)
    btnGenerate.addEventListener('click', function () {
        idInput.forEach(elId => {
            if (elId === 'ba_add_login') {
                document.getElementById(elId).value = 'sl_l' + generateSecure(28)
            }
            if (elId === 'ba_add_password') {
                document.getElementById(elId).value = 'sl_p' + generateSecure(28)
            }

        })
    })
}

function addLogAndPass() {
    let data = {}
    const btnAdd = document.querySelector(`#ba_add_add`)
    btnAdd.addEventListener('click', async function () {

        const log = document.getElementById('ba_add_login').value.trim()
        const pass = document.getElementById('ba_add_password').value.trim()
        const isValid = await checkData([log, pass], 'base_auth');

        if (!log || !pass || !pass.startsWith('sl_p') || !log.startsWith('sl_l') || log.length < 32 || pass.length < 32 || !isValid) {
            alertMessage('Некоректні дані для BASE AUTH, подивіться інструкцію вище!', 'error');
            return;
        }

        data['login'] = log
        data['pass'] = pass
        await handleOption('sl_add_option', data, 'base_auth')
        document.getElementById('ba_add_login').value = ''
        document.getElementById('ba_add_password').value = ''
    })


}
async function loadLogAndPass(data) {
    const previewContainer = document.querySelector('.base_auth_preview')
    const pcContent = previewContainer.querySelector('ul')
    pcContent.innerHTML = ''
    data.forEach(logAndPass => {
        const liItem = document.createElement('li')

        const spanLog = document.createElement('span')
        spanLog.textContent = logAndPass['login']
        const spanPass = document.createElement('span')
        spanPass.textContent = logAndPass['pass']
        const inputRemove = document.createElement('input')
        inputRemove.type = 'button'
        inputRemove.value = 'X'
        inputRemove.addEventListener('click', async function () {
            await handleOption('sl_remove_option', logAndPass, 'base_auth')
            await loadLogAndPass(await handleOption('sl_get_option', [], 'base_auth'))
        })
        liItem.appendChild(spanLog)
        liItem.appendChild(spanPass)
        liItem.appendChild(inputRemove)
        pcContent.appendChild(liItem)
    })
    previewContainer.appendChild(pcContent)

}


document.addEventListener("DOMContentLoaded", async function () {

    generateLogAndPass()
    addLogAndPass()
    await loadLogAndPass(await handleOption('sl_get_option', [], 'base_auth'))
})
