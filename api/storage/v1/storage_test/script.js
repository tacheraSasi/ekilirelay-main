document.getElementById('uploadForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const apikeyInput = document.getElementById('apikeyInput');
  const fileInput = document.getElementById('fileInput');
  const apiKey = apikeyInput.value.trim();
  const file = fileInput.files[0];
  const messageDiv = document.getElementById('message');
  const uploadButton = document.getElementById('uploadButton');

  if (!apiKey) {
    messageDiv.innerHTML = `<div class="mt-4 p-4 bg-red-50 border border-red-400 text-red-800 rounded">
        <p class="font-semibold">Missing API Key</p>
        <p>Please enter your API key. Visit <a href="https://relay.ekilie.com/console" target="_blank" class="underline font-medium">Ekilirelay Console</a> to get one if you don't have one.</p>
      </div>`;
    return;
  }
  if (!file) {
    messageDiv.innerHTML = `<div class="mt-4 p-4 bg-red-50 border border-red-400 text-red-800 rounded">
        <p class="font-semibold">No File Selected</p>
        <p>Please select a file to upload.</p>
      </div>`;
    return;
  }
  const formData = new FormData();
  formData.append('apikey', apiKey);
  formData.append('file', file);
  uploadButton.disabled = true;
  messageDiv.innerHTML = `<div class="flex justify-center items-center space-x-2">
      <svg class="animate-spin h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      <span class="text-green-600">Uploading...</span>
    </div>`;
  try {
    const response = await fetch('https://relay.ekilie.com/api/storage/v1/index.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    if (!response.ok) throw new Error(result.message);
    const originalName = result.metadata.original_name;
    const fileNameWithoutExt = originalName.split('.').slice(0, -1).join('.');

    messageDiv.innerHTML = `<div class="mt-4 p-4 text-wrap bg-green-50 border border-green-400 text-green-800 rounded">
        <p class="font-semibold">Upload Successful!</p>
        <p>Filename: <span class="font-mono">${fileNameWithoutExt}</span></p>
        <p>URL: <a href="${result.url}" target="_blank" class="underline font-medium text-sm">${result.url}</a></p>
      </div>`;
    console.log('Upload successful:', result);
  } catch (error) {
    console.error('Error:', error);
    messageDiv.innerHTML = `<div class="mt-4 p-4 bg-red-50 border border-red-400 text-red-800 rounded">
        <p class="font-semibold">Upload Failed</p>
        <p>${error.message}</p>
      </div>`;
  } finally {
    uploadButton.disabled = false;
  }
});