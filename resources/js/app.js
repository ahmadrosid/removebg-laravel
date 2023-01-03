import './bootstrap';
import Compressor from 'compressorjs';

const file = document.getElementById('file');

file.addEventListener('change', (e) => {
  const file = e.target.files[0];

  if (!file) {
    return;
  }

  new Compressor(file, {
    quality: 0.75,
    size: 4,

    // The compression process is asynchronous,
    // which means you have to access the `result` in the `success` hook function.
    success(result) {
      const formData = new FormData();

      // The third parameter is required for server
      formData.append('file', result, result.name);

      // Send the compressed image file to server with XMLHttpRequest.
      axios.post('/path/to/upload', formData).then(() => {
        console.log('Upload success');
      });
    },
    error(err) {
      console.log(err.message);
    },
  });
  
});
