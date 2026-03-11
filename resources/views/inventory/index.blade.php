<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="oten_form">
        <form action="" method="POST">
            @csrf

            <input type="text" name="item_category_name">
            <button name="submit">TUPLOKA KO NIGGA</button>
        </form>
        @if(session('success'))
        <p>nasulod nas database nigga</p>
        @endif
    </div>
</body>
</html>