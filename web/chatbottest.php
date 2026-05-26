<?php
$id = (int)$_REQUEST['id'] ?? 0;
$pk = (int)$_REQUEST['pk'] ?? 0;
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Тестирование чат бота</title>
</head>
<body>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            window.Smartius = {
                testchatbot: <?=$id?>,
                apiUrl: '<?=$_SERVER['SERVER_ADDR']?>/api',
                staticUrl: '<?=$_ENV['DOMAININFOSTATICWIDGET']?>',
                publicKey: <?=$pk?>,
                _user: {
                    id: 1234,
                    role: null,
                    name: null,
                    email: null
                }
            };
            var script = document.createElement('script');
            script.src = '//<?=$_ENV['DOMAININFOSTATICWIDGET']?>/lib.js', document.head.appendChild(script);
        }, 1000);
    });
</script>
</body>
</html>





