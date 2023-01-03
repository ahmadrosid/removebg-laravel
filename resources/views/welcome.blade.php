<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Remove Background Laravel with PhotoRoom API</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .cp-spinner {
            width: 48px;
            height: 48px;
            display: inline-block;
            box-sizing: border-box;
            position: relative
        }

        .cp-round:before {
            border-radius: 50%;
            content: " ";
            width: 48px;
            height: 48px;
            display: inline-block;
            box-sizing: border-box;
            border-top: solid 6px #bababa;
            border-right: solid 6px #bababa;
            border-bottom: solid 6px #bababa;
            border-left: solid 6px #bababa;
            position: absolute;
            top: 0;
            left: 0
        }

        .cp-round:after {
            border-radius: 50%;
            content: " ";
            width: 48px;
            height: 48px;
            display: inline-block;
            box-sizing: border-box;
            border-top: solid 6px #7c3aed;
            border-right: solid 6px transparent;
            border-bottom: solid 6px transparent;
            border-left: solid 6px transparent;
            position: absolute;
            top: 0;
            left: 0;
            animation: cp-round-animate 1s ease-in-out infinite
        }

        @keyframes cp-round-animate {
            0% {
                transform: rotate(0)
            }

            100% {
                transform: rotate(360deg)
            }
        }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-700">
    <div class="h-screen w-screen flex items-center justify-center">
        <form class="border bg-white rounded shadow-sm dark:bg-gray-600 w-full max-w-4xl">
            <div class="bg-gray-200 dark:bg-gray-800 p-4 rounded-t">
                <h2 class="font-bold text-4xl dark:text-gray-300">Remove Background</h2>
            </div>
            <div class="p-4">
                <div class="w-full flex loading-container"></div>
                <div class="photo-container grid grid-cols-2 gap-4 max-w-3xl mx-auto"></div>
                <input type="file" id="file" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-800
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-violet-50 file:text-violet-700
                        hover:file:bg-violet-100
                        hover:file:cursor-pointer
                        mx-auto
                        bg-gray-200 p-3 dark:bg-gray-500
                        "/>
            </div>
        </form>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.0/dist/browser-image-compression.js"></script>
<script src="https://unpkg.com/axios@1.2.2/dist/axios.min.js"></script>
<script>
    const file = document.getElementById('file');

    file.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        console.log(`originalFile size ${(file.size / 1024 / 1024).toFixed(1)} MB`);

        const options = {
            maxSizeMB: 1,
            maxWidthOrHeight: 1920,
            useWebWorker: true
        }

        const loadingContainer = document.querySelector(".loading-container")
        loadingContainer.innerHTML = '<div class="cp-spinner mx-auto cp-round my-16"></div>';

        try {
            const result = await imageCompression(file, options);
            console.log(`compressed image size ${(result.size / 1024 / 1024).toFixed(1)} MB`);

            const formData = new FormData();
            formData.append('photo', result, result.name);
            const res = await axios.post('/api/remove-bg', formData);

            loadingContainer.innerHTML = "";
            displayResult(res.data);
        } catch (err) {
            console.log(err);
            loadingContainer.innerHTML = `<p class="py-8 text-2xl text-red-500">${err.message}</p>`;
        }
    });

    function displayResult(data) {
        const container = document.querySelector(".photo-container");
        container.innerHTML = `
            <div class="grid place-items-center gap-4 pb-8">
                <h3 class="text-2xl font-bold">Before</h3>
                <img src="${data.original}" />
            </div>
            <div class="grid place-items-center gap-4 pb-8">
                <h3 class="text-2xl font-bold">After</h3>
                <img src="${data.result}" />
            </div>
        `;
    }
</script>

</html>