<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyCaffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div id="app">
        <nav class="bg-gray-800 text-white p-4">
            <div class="container mx-auto">
                <a href="/" class="text-xl font-bold">MyCaffee</a>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>