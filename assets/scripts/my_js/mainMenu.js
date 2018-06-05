console.log("mainMenu.js успешно подключён ...");
window.addEventListener("DOMContentLoaded", mainMenuInit);

//запрос страницы меню middleContentDiv
function getPage(pageName) {
    console.log("Запрашиваем страницу: " + pageName );
    var pageRequest = new AjaxReq();
    var url = "/ajax/GetPage/" + pageName;
    var middleContentDiv = document.getElementById("middleContent");
    middleContentDiv.innerHTML = "";
    pageRequest.requestGet(url, false);
    middleContentDiv.innerHTML = pageRequest.serverAnswer;
    //init();
}

function mainMenuInit() {
    "use strict"

    console.log("запускаю функцию mainMenuInit ...");

    //##### Главное меню Start #####

    //элементы меню
    class Menu {
        //принимаем массив с id элементов <a> ссылок меню
        constructor(idArray) {
            console.log("Создаю объект класса Menu");
            var that = this;
            idArray.map(function(x) {
                //console.log(x);
                if (document.getElementById(x) != null)
                    that[x] = document.getElementById(x);

            });
        }
    }

    //прописываем события пунктам главного меню
    //Menu = new Menu(["home", "trening", "diet", "contacts", "login_ms", "registration_ms"]);
    Menu = new Menu(["login_ms", "registration_ms"]);
    //console.log(Menu.home.id);
    /*Menu.home.addEventListener("click", function (){
        menuFocusOff(); //деактивируем состояние "Активный" у всех пунктов меню
        getPage('mainPage'); //получаем контенет страницы
        //$("mainPageText").animate({height: 'show'}, 500);
        var elemLi = Menu.home.parentNode;
        elemLi.className = "active"; //активируем пункт меню
        window.history.pushState(null, null, "/home");
    });*/


    /*Menu.trening.addEventListener("click", function (){
        menuFocusOff(); //деактивируем состояние "Активный" у всех пунктов меню
        getPage('trening');
        var elemLi = Menu.trening.parentNode;
        elemLi.className = "active"; //активируем пункт меню
        window.history.pushState(null, null, "/trening/site/v/2");

        //проверяем наличие элемента "кнопка"
        //if (myCheckElem("buttonOrderProgram")) {
        //    console.log("Вешаю событие на кнопку заказа формы из trening");
        //    if (buttonElem == null) {
        //        var buttonElem = document.getElementById("buttonOrderProgram"); //Получаем кнопку
        //        buttonElem.addEventListener("click", clickOrderProgramButton); //прописываем buttonOrderProgram на событие "click"
        //    }
        //}
    });

    Menu.diet.addEventListener("click", function (){
        menuFocusOff(); //деактивируем состояние "Активный" у всех пунктов меню
        getPage('diet');
        var elemLi = Menu.diet.parentNode;
        elemLi.className = "active"; //активируем пункт меню
        window.history.pushState(null, null, "/diet/site/v/3");
    });

    Menu.contacts.addEventListener("click", function (){
        menuFocusOff(); //деактивируем состояние "Активный" у всех пунктов меню
        getPage('contacts');
        var elemLi = Menu.contacts.parentNode;
        elemLi.className = "active"; //активируем пункт меню
        window.history.pushState(null, null, "/contacts");

        function captchaDeleteImg() {
            var RequestСaptchaDeleteImg = new AjaxReq();
            console.log("captchaDeleteImg: Отправляю запрос на удаление картинки из МЕНЮ");
            RequestСaptchaDeleteImg.requestGet("/ajax/captchaPictureDelete", true);
        }
        setTimeout(captchaDeleteImg, 3000);
    });
*/
    if (Menu.login_ms != null) {
        Menu.login_ms.addEventListener("click", function (){
            //menuFocusOff(); //деактивируем состояние "Активный" у всех пунктов меню
            getPage('Login'); //получаем контенет страницы
            window.history.pushState(null, null, "/Login");
        });
    }

    if (Menu.registration_ms != null) {
        Menu.registration_ms.addEventListener("click", function (){
            //menuFocusOff(); //деактивируем состояние "Активный" у всех пунктов меню
            getPage('Registration'); //получаем контенет страницы
            window.history.pushState(null, null, "/Registration");
        });
    }

    //деактивация состояния "Активный" у всех пунктов меню
    function menuFocusOff() {
        var elemUl = document.getElementById("mainMenuUl");
        var elemsLi = elemUl.getElementsByTagName("li");
        //console.log(elemsLi);
        for (var i = 0; i < elemsLi.length; i++) {
            elemsLi[i].className = "";
        }
    }
    

    //##### Главное меню END #####
}