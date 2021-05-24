const url = document.location.origin + '/colege_project/server/api.php';
const download_url = document.location.origin + '/colege_project/server/';

const end_mess = 'Вы дошли до конца';
const empty_mess = 'Эта страница пустая';
const add_mess = 'Больше';

let last_id = 0;
let stats = 0;
let message_set = '';

function append_section(array) {
    return $('.content').append('<div class="content_head"><div class="content_block"><a href="image/full/' + array['way'] + '" target="_blank"><div class="content_block_img"><img class="content_img" src="image/full/' + array['way'] + '" alt=""></div><h4>' + array['tags'].split(' ').slice(0, 2).join(' ') + '</h4></a></div><div class="content_block_action"><button class="content_block_action_button button_origin tooltip" data-title="' + (array['origin'] === null ? 'Источник отсутствует' : 'Источник') + '" href="' + (array['origin'] === null ? 'empty' : array['origin']) + '" style="border-radius: 0 0 0 16px;"><img class="image_add" src="assets/icon/info.png"></button><button value="image/full/' + array['way'] + '" class="content_block_action_button button_copy tooltip" data-title="Скопировать ссылку"><img class="image_add" src="assets/icon/copy.png"></button><button class="content_block_action_button button_download tooltip" value="image/full/' + array['way'] + '" data-title="Скачать" style="border-radius: 0 0 16px 0;"><img class="image_add" src="assets/icon/download.png"></button></div></div>');
}

function get_stats() {
    $.get(url, {id : 0, type : 'stats', message : 0}, function(result) {
        result = JSON.parse(result);
        if (result['Error'] === false) {
            last_id = result['Message'][0]['count'];
            stats = result['Message'][0]['less'];
        }
    });
}


// https://www.pandoge.com/moduli-i-skripty/skachivanie-faylov-po-ssylke-na-javascript //
// Функция с этого сайта. Супер большое спасибо автору. //
function get_file_url(url) {
	var link_url = document.createElement("a");
	link_url.download = url.substring((url.lastIndexOf("/") + 1), url.length);
	link_url.href = url;
	document.body.appendChild(link_url);
	link_url.click();
	document.body.removeChild(link_url);
	delete link_url;
}

function set_mess(id, type, message = false) {
    if (id == 1) {
        $('.search_but').replaceWith('<h4 id="end">' + (message == false ? end_mess : empty_mess) + '</h4>');
        $('.index_but').replaceWith('<h4 id="end">' + (message == false ? end_mess : empty_mess) + '</h4>');
    }else{
        $('.search_but').replaceWith('<button class="add_button ' + type + '">' + add_mess + '</button>');
        $('.index_but').replaceWith('<button class="add_button ' + type + '">' + add_mess + '</button>');
        $('#end').replaceWith('<button class="add_button ' + type + '">' + add_mess + '</button>');
    }
}

function search_start(val) {
    $('.content').empty();
    message_set = val;
    last_id = 0;
    if (val === '') {
        get_stats();
        set_mess(0, 'index_but');
        get_image(last_id, 'index', 'FIRST');
    }else{
        set_mess(0, 'search_but');
        get_image(0, 'search', 'FIRST', $('#input').val());
    }
}

function get_image(id, type, mess, search = null) {
    if(search !== null){
        $.get(url, {id : id, type : type, message : mess, search : search}, function(result) {
            result = JSON.parse(result);
            if (result['Error'] === false) {
                if(result['Message'] === false){
                    set_mess(1, 'search_but', true);
                    return true;
                }else{
                    set_mess(0, 'search_but');
                }
                result['Message'].forEach(element => {
                    if (element['id'] == result['Last']) {
                        set_mess(1, 'search_but');
                    }else set_mess(0, 'search_but');
                    append_section(element); 
                    last_id++;
                });   
            }
        });
        return true;
    }
    $.get(url, {id : id, type : type, message : mess, search : search}, function(result) {
        result = JSON.parse(result);
        console.log(result);
        if (result['Error'] === false) {
            result['Message'] === false ? set_mess(1, 'index_but') :set_mess(0, 'index_but');
            result['Message'].forEach(element => {
                if (element['id'] == result['Last']) {
                    set_mess(1, 'index_but');
                }else set_mess(0, 'index_but');
                append_section(element); 
                last_id++;
            });   
        }
    });
}

$(document).ready(function() {
    //get_stats();

    $('#button').click(function() {
        search_start($('#input').val());
    });

    // https://webformyself.com/kak-pojmat-sobytie-nazhatiya-enter-v-pole-input
    // Инфу на этом сайте нашел.
    $('#input').keydown(function(e) {
        if(e.keyCode === 13) {
            search_start($(this).val());
        }
    });

    $('body').on('click', '.index_but', function(){
        console.log(last_id);
        get_image(last_id, 'index', 'ADD');
    });

    $('body').on('click', '.search_but', function(){
        get_image(last_id, 'search', 'ADD', message_set);
    });

    $('body').on('click', '.button_download', function(){
        get_file_url(download_url + $(this).attr("value"));
    });

    $('body').on('click', '.button_origin', function(){
        if($(this).attr('href') !== 'empty') window.open($(this).attr('href'));
    });

    $('body').on('click', '.button_copy', function(){
        let element_cl = $(this);
        let fk = document.createElement("textarea");
        let text = element_cl.attr("data-title");
        document.body.appendChild(fk);
        fk.value = document.location.origin + '/' + element_cl.attr("value");
        element_cl.attr("data-title", 'Скопировано!');
        setTimeout(function() {
            element_cl.attr("data-title", text);
          }, 2000); 
        fk.select();
        document.execCommand("copy");
        document.body.removeChild(fk);
    });
});