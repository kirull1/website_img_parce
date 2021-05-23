<?php include('layouts/doctype.php') ?>
<body>
    <section class="search">
        <div class="search_div" style="width: 40%;">
            <h1>Поиск картинок</h1>
            <div class="search_input">
                <input id="input" class="input_search" type="text" placeholder="Поиск">
                <button id="button" class="button_search" type="button"><img src="assets/icon/loop.png" class="image_search"></button>
            </div>
        </div>
    </section>

    <section class="content">
        <?php
            $options = [
                'id' => true,
                'type' => 'index',
                'message' => 'FIRST',
                'search' => null,
            ];
            foreach(json_decode(file_get_contents('http://localhost:8008/colege_project/server/api.php?' . http_build_query($options)))->Message as $content)
                echo '<div class="content_head"><div class="content_block"><a href="image/full/' . $content->way . '" target="_blank"><div class="content_block_img"><img class="content_img" src="image/full/' . $content->way . '" alt=""></div><h4>' . implode(' ', array_slice(explode(' ', $content->tags), 0, 2)) . '</h4></a></div><div class="content_block_action"><button class="content_block_action_button button_origin tooltip" data-title="' . ($content->origin === null ? 'Источник отсутствует' : 'Источник') . '" href="' . ($content->origin === null ? 'empty' : $content->origin) . '" style="border-radius: 0 0 0 16px;"><img class="image_add" src="assets/icon/info.png"></button><button value="image/full/' . $content->way . '" class="content_block_action_button button_copy tooltip" data-title="Скопировать ссылку"><img class="image_add" src="assets/icon/copy.png"></button><button class="content_block_action_button button_download tooltip" value="image/full/' . $content->way . '" data-title="Скачать" style="border-radius: 0 0 16px 0;"><img class="image_add" src="assets/icon/download.png"></button></div></div>';
        ?>
    </section>

    <div class="div_add">
        <button class="add_button index_but">
            Больше
        </button>
    </div>

<?php include('layouts/footer.php') ?>