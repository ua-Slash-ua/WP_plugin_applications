async function loadChooseTypeOrLabel(data,selectId){
    if (!Array.isArray(data)) {
        data = [data]
    }
    console.log(data)
    const selectType = document.querySelector(`#${selectId}`)
    data.forEach(type =>{
        const optionType = document.createElement('option')
        optionType.value = type.slug
        optionType.textContent = type.name
        selectType.appendChild(optionType)
    })
}

function addEndpoint(){
    async function getEndpointData(){
        let data = {}
        const edName = document.getElementById('ep_add_text_name')
        const edPathEnd = document.getElementById('ep_add_path_end')
        const edType = document.getElementById('ep_add_type')
        const edLabels = document.getElementById('ep_add_labels')

        let name = edName.value.trim()
        let pathEnd = edPathEnd.value.trim()


        let typeSlug = edType.value.trim()
        let labels = Array.from(edLabels.selectedOptions).map(option => option.value);


        data['name'] = name
        data['path_end'] = pathEnd
        data['type'] = typeSlug
        data['labels'] = labels
        console.log(data)
        return data
    }
    const selectType = document.getElementById('ep_endpoint_btn_add')
    selectType.addEventListener('click', async function (){
        let data = await getEndpointData()
    })
}

document.addEventListener("DOMContentLoaded", async function () {
    await loadChooseTypeOrLabel(await handleOption('sl_get_option',[],'endpoint_label'),'ep_add_labels')
    await loadChooseTypeOrLabel(await handleOption('sl_get_option',[],'endpoint_type'),'ep_add_type')
    addEndpoint()
})