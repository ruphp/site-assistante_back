const requestButton = document.querySelector('[data-request-sms-code]');
const phoneInput = document.querySelector('[name="phone"]');
const codeForm = document.querySelector('[data-sms-code-form]');
const codeInput = document.querySelector('[name="sms_code"]');
let requestId = null;

requestButton?.addEventListener('click', async () => {
  requestButton.disabled = true;
  try {
    const response = await fetch('/api/registration/phone/request', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({phone: phoneInput.value}),
    });
    const data = await response.json();
    if (!response.ok) throw new Error(data.message || 'Не удалось запросить код');
    requestId = data.requestId;
    codeForm.hidden = false;
  } catch (error) {
    alert(error.message);
  } finally {
    requestButton.disabled = false;
  }
});

codeForm?.addEventListener('submit', async event => {
  event.preventDefault();
  const response = await fetch('/api/registration/phone/verify', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({requestId, code: codeInput.value}),
  });
  const data = await response.json();
  if (!response.ok) return alert(data.message || 'Код не принят');
  window.location.reload();
});
