
async function loadApplications() {
    console.log(await handleApplication('sl_get_applications',[]))
}
document.addEventListener("DOMContentLoaded", async function () {

    await loadApplications()
})