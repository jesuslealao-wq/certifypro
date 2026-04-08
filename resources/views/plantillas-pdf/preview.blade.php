<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preview Plantilla</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #525659;
            font-family: 'Times New Roman', serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            gap: 30px;
        }
        .page-break { page-break-after: always; }
        .page[data-bg] {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        {!! $css !!}
    </style>
</head>
<body>
    {!! $html !!}

    <script>
        const fondos = @json($fondos);
        const pages = document.querySelectorAll('.page, .page-break');
        let pageIndex = 0;
        pages.forEach(el => {
            pageIndex++;
            const bgUrl = fondos[pageIndex];
            if (bgUrl) {
                el.style.backgroundImage = "url('" + bgUrl + "')";
                el.style.backgroundSize = "cover";
                el.style.backgroundPosition = "center";
                el.style.backgroundRepeat = "no-repeat";
            }
            if (el.classList.contains('page-break') && !el.classList.contains('page')) {
                // If this is only a page-break separator, skip counting the next
            }
        });
    </script>
</body>
</html>
