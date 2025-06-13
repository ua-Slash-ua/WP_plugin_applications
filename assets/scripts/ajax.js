/**
 * Асинхронний виклик PHP AJAX функції у WordPress
 * @param {string} action - назва AJAX функції у WordPress
 * @param {Object} data - дані у форматі JSON для передачі у PHP
 * @returns {Promise} - Promise з результатом виконання запиту
 */
async function callWpAjaxFunction(action, data = {}) {
    try {
        const response = await fetch(ajax_object.ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                action: action,
                data: JSON.stringify(data)
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
        }

        const result = await response.json();

        if (result.error) {
            throw new Error(result.error);
        }

        return result;
    } catch (error) {
        console.error('AJAX Error:', error);
        throw error;
    }
}
