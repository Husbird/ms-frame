//скрипты текущего приложения
console.log("app.js успешно подключён");
//добавляем действие - init к событию - полная загрузка страницы
window.addEventListener("DOMContentLoaded", init);

Request = new AjaxReq(); //создаём объект XMLHttpRequest

//###### Кнопка открытия формы анкеты для заказа программы тренировок Start #####
//при клике по кнопке "Заказать программу":
function clickOrderProgramButton() {
    //прячем кнопку и текст
    console.log("open form!");
    var buttonElem = document.getElementById("buttonOrderProgram"); //Получаем кнопку
    buttonElem.style.display = "none";
    var mainPageTextElem = document.getElementById("mainPageText"); //Получаем блок с тектсом
    mainPageTextElem.innerHTML = "";
    //Формируем заголовок формы
    var p_titleText = document.createTextNode("Форма для заказа персональной программы:");
    var elemP_title = document.createElement("p");
    elemP_title.id = "formTitle";
    //вставляем элементы
    elemP_title.appendChild(p_titleText);
    mainPageTextElem.appendChild(elemP_title);

    Request.showAsyncGetAnswer = function(answer) {
        var formDiv = document.createElement("div");
        formDiv.id = "formDiv";
        formDiv.innerHTML = answer;
        mainPageTextElem.appendChild(formDiv);
        //вешем событие на кнопку "Готово"
        var formSendButton = document.getElementById("formSendButton");
        formSendButton.addEventListener("click", clickFormSendButton);//прописываем formSendButton на событие "click"

        //вешем событие на кнопку "Отмена"
        var formCancelButton = document.getElementById("formHideButton");
        formCancelButton.addEventListener("click", formHideButton);

    }
    //Request.requestGet("/ajax/forms/_user_info_1.php", true);
    Request.requestGet("/ajax/GetFormTrainOrder", true);

}

//###### Кнопка отправки формы анкеты для заказа программы тренировок Start #####

//класс структура отправляемых данных из формы
class UserInfo {
    constructor() {
        console.log("Создаю объект класса \"UserInfo\"");
        this.source = null; //источник данных (указать обязательно для парсера)

        this.age = null;
        this.sex = null;
        this.health = null;
        this.experience = null;
        this.physExercise = null;
        this.purpose = null;
        this.note = null;
        this.userName = null;
        this.email = null;
        this.viber = null;
        this.skype = null;
    }
}

function clickFormSendButton() {
    console.log("Нажали кнопку \"Готово!\"");
    var FormData = new UserInfo(); //создаём объект данных формы
    FormData.source = "FormTrainOrder"; //указываем источник данных

    //получаем возраст
    var ageElem = document.getElementById("age");
    FormData.age = ageElem.value; //console.log(age);

    //получаем пол
    var sexElem = document.getElementsByName("sex"); //console.log(sexElem);
    FormData.sex = getRadioValue(sexElem); //console.log(sex);

    //Проблемы со здоровьем
    var healthElem = document.getElementById("health");
    FormData.health = healthElem.value; //console.log(health);

    //Стаж занятий спортом
    var experienceElem = document.getElementById("experience");
    FormData.experience = experienceElem.value; //console.log(experience);

    //Физические нагрузки без занятий спортом
    var physExerciseElem = document.getElementById("physExercise");
    FormData.physExercise = physExerciseElem.value; //console.log(physExercise);

    //Питание
    var foodElem = document.getElementById("food");
    FormData.food = foodElem.value; //console.log(food);

    //Цель занятий
    var purposeElem = document.getElementById("purpose");
    FormData.purpose = purposeElem.value; //console.log(purpose);

    //Дополнительная информация
    var noteElem = document.getElementById("note");
    FormData.note = noteElem.value; //console.log(note);

    //Имя пользователя
    var userNameElem = document.getElementById("userName");
    FormData.userName = requireInputValidate(userNameElem.value, "Ваше имя"); //console.log(userName);
    if (!FormData.userName) {
        userNameElem.style.backgroundColor = "yellow";
        userNameElem.addEventListener("click", function () {
            userNameElem.style.backgroundColor = "white";
        });
        return false;
    }

    // получаем и проверяем email
    var emailElem = document.getElementById('email');
    FormData.email = emailValidate(emailElem.value, "E-mail"); //console.log(email);
    console.log();
    if (!FormData.email) {
        emailElem.style.backgroundColor = "yellow";
        emailElem.addEventListener("click", function () {
            emailElem.style.backgroundColor = "white";
        });
        return false;
    }

    //Viber
    var viberElem = document.getElementById("viber");
    FormData.viber = viberElem.value; //console.log(viber);

    //Skype
    var skypeElem = document.getElementById("skype");
    FormData.skype = skypeElem.value; //console.log(skype);

    //сериализуем в JSON
    var jsonFormData = JSON.stringify(FormData);
    console.log(jsonFormData);

    //готовим метод для вывода ответа сервера на за запрос
    Request.showAsyncPostAnswer = function (serverAnswer) {
        var mainPageTextElem = document.getElementById("mainPageText"); //Получаем блок с тектсом
        mainPageTextElem.innerHTML = "";
        var bodyElem = document.getElementsByTagName("body"); //получаем элемент body
        bodyElem[0].style.backgroundColor = "gray";
        mainPageTextElem.innerHTML = serverAnswer;
        //получаем кнопку "Ок" блока сообщения
        var infoSavedButtonOk = document.getElementById("infoSavedButtonOk");
        //прописываем действие по клику для кнопки "Ок"
        infoSavedButtonOk.addEventListener("click", function () {
            console.log("запрашиваем исходник страницы Тренинг");
            bodyElem[0].style.backgroundColor = "white";

            //запрашиваем страницу тренинг
            getPage("trening");
            mainPageTextElem.innerHTML = "";
        });

    }

    //отправляем данные
    Request.requestPost("/", true, jsonFormData);
}

//####################### Вспомогательные функции #################################

//получаем значение радиокнопок (nodeListArray - массив елементов типа radio полученых например так:
//  var bibleElem = document.getElementsByName("bible"); (где атрибут name="bible"))
function getRadioValue(nodeListArray) {
    for(var i = 0; i < nodeListArray.length; i++){
        if(nodeListArray[i].checked) {
            //console.log(nodeListArray[i].value);
            return nodeListArray[i].value;
        }
    }
    return false;
}

//валидация правильности указания e-mail, emailString - введённое значение;
// fieldName - название поля (для вывода сообщения пользователю)
function emailValidate(emailString, fieldName) {
    if (emailString == "") {
        console.log(`Ошибка: заполните обязательное поле "${fieldName}"`);
        return false;
    }
    // регулярка для проверки
    var email_regexp = /[0-9a-zа-я_A-ZА-Я]+@[0-9a-zа-я_A-ZА-Я^.]+\.[a-zа-яА-ЯA-Z]{2,4}/i;
    // проверяем значение поля email, если нет, то:
    if (!email_regexp.test(emailString)) {
        console.log("Ошибка: введён некорректный email !");
        //alert('Проверьте email');
        return false;
    }

    return emailString;
}

//Валидация обязательного поля valueString- введённое значение, fieldName - название поля (для вывода
// сообщения пользователю)
function requireInputValidate(valueString, fieldName) {
    if (valueString == "") {
        console.log(`Ошибка: заполните обязательное поле "${fieldName}"`);
        return false;
    }

    return valueString;
}


//###### Кнопка отправки формы анкеты для заказа программы тренировок END #####

// ######  кнопка отмены формы анкеты для заказа программы тренировок START #####
function formHideButton() {
    console.log('Нажали кнопку ОТМЕНА');
    getPage('trening');
}
// ######  кнопка отмены формы анкеты для заказа программы тренировок END #####


function init() {
    "use strict"

    console.log("запускаю функцию init приложения...");

    //##### Основнык элементы ####

    

    //#### Основные элементы END ####

    //##### Футер Start #####

    //устанавливаем минимальную высоту блока с контентом с учётом окна документа
    //прижимаем футер к низу экрана
    function getFooterDown() {
        var windowSize = window.windowSize();

        //получаем элемент с контентом
        var contentBlock = document.getElementById("mainContentMS");
        //получаем футер
        var footerElem = document.getElementById("footer");
        //получаем хедер
        var headerElem = document.getElementById("header");

        //получаем объект стилей футера
        var footerElementStyle = getComputedStyle(footerElem, null);
        //получаем объект стилей хедера
        var headerElementStyle = getComputedStyle(headerElem, null);

        //получаем значения свойств футера
        var footerHeight = footerElementStyle.minHeight;
        var footerMarginTop = footerElementStyle.marginTop;

        //получаем значения свойств хедера
        var headerHeight = headerElementStyle.minHeight;

        //избавляемся от "px"
        footerHeight = parseInt(footerHeight, 10);
        footerMarginTop = parseInt(footerMarginTop, 10);
        headerHeight = parseInt(headerHeight, 10);

        //учитываем высоту футера - устанавливаем высоту блока с контентом
        var newHeight = windowSize.height - footerMarginTop - footerHeight - headerHeight;
        contentBlock.style.minHeight = newHeight + "px";
        //console.log(footerMarginTop);
    }
    //прижимам футер к низу окна
    getFooterDown();

    //##### Футер END #####



}