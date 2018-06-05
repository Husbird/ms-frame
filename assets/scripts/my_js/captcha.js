/**
 * Created by User on 08.05.2017.
 */
//скрипты текущего приложения
console.log("captcha.js успешно подключён");
//добавляем действие - init к событию - полная загрузка страницы
//window.addEventListener("DOMContentLoaded", captcha_ms);

//обновляем картинку с кодом
function reloadCaptchaImg() {
    console.log("reloadCaptchaImg: обновить картинку");
    Request.showAsyncGetAnswer = function (serverAnswer) {
        var imgElem = document.getElementById("captchaImg");
        console.log(imgElem);
        //выводим новую картинку
        imgElem.src = "/assets/temp/captcha/pic_" + serverAnswer + ".gif";

        //удаляем картинку с сервера
        // с задержкой, чтобы браузер успел загрузить картинку
        function second_passed() {
            Request2 = new AjaxReq();
            Request2.requestGet("/ajax/captchaPictureDelete", true);
        }
        setTimeout(second_passed, 3000);

        //Request2 = new AjaxReq();
        //Request2.requestGet("/ajax/captchaPictureDelete", true);
    };
    Request.requestGet("/ajax/captchaPictureRefresh", true);
}



/*function captcha_ms() {
    //удаляем картинку с сервера
    //Request2 = new AjaxReq();
    function captchaDeleteImg() {
        console.log("captchaDeleteImg: Отправляю запрос на удаление картинки");
        Request.requestGet("/ajax/captchaPictureDelete", true);
    }
    captchaDeleteImg();
}*/
