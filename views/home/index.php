<!DOCTYPE html>
<html>
<head>
    <title><?=$message?></title>
</head>
<body>
    <?=$message?>
    <?php foreach ($items as $item): ?>
        <p><?=$item['id']?>. <?=$item['name']?></p>
    <?php endforeach;?>
</body>
</html>