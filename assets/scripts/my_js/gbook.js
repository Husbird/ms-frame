/**
 * Created by User on 08.05.2017.
 */
//скрипты текущего приложения
console.log("gbook.js успешно подключён");
//добавляем действие - init к событию - полная загрузка страницы
window.addEventListener("DOMContentLoaded", gbook_ms);



function gbook_ms() {
//удаляем картинку с сервера
//Request2 = new AjaxReq();
    console.log("gbook_ms: Отправляю запрос на удаление картинки");
    Request.requestGet("/ajax/gBookPictureDelete", true);
}