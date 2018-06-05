/* 
My first JS library
designed by ms-projects 
started: 12/03/2017
*/ 

//"use strict"
console.log("myLibrary.js - успешно подключён");
/*
Класс AjaxReq
служит для выполнения синхронных и асинхронных AJAX запросов и 
обработки ответов сервера.
 
constructor() - создаёт объект XMLHttpRequest

requestGet(url, method) - 
-реализует AJAX запрос к серверу по указанному в url адресу методом "GET"
-url - адрес файла на сервере; пример: gettime.php?delay=3
-method - метод запроса: true - асинхронный; false - асинхронный
-полученный ответ полученный синхронно присваивает свойству this.serverAnswer
-полученный ответ полученный асинхронно отдаёт методу showAnswerAsyncRequest(serverAnswer)

showAnswerAsyncRequest(serverAnswer) -
-serverAnswer - ответ сервера
-Вывод результата асинхронного запроса (нужно расширить по ситуации)
1) Через прототип: AjaxReq.prototype.showAnswerAsyncRequest = function(serverAnswer) {}
    или
2) Переопределение метода: myRequest.showAnswerAsyncRequest = function(serverAnswer) {}
*/
class AjaxReq {
    constructor() {
        console.log("Создаю объект класса \"AjaxReq\"");
        this.request = null; //объект XMLHttpRequest
        this.currentMethod = null; //текущий метод (синхронный\асинхронный)
        this.serverAnswer = null; //здесь будет ответ сервера только на СИНХРОННЫЙ запрос
        this.info = null; //информация вернувшихся заголовков
        this.errors = []; //ошибки
        //var that = null;
        this.getXMLHttpRequest_();
        //console.log(this.showAnswerAsyncRequest);
        //console.log(this);
    }

    //Кроссбраузерное создание объекта запроса XMLHttpRequest
    getXMLHttpRequest_() {
        if (window.XMLHttpRequest) {
            //console.log("Создаю объект XMLHttpRequest...");
            try { 
                this.request = new XMLHttpRequest(); 
                if (typeof(this.request) == "object") {
                    console.log("Объект XMLHttpRequest успешно создан");
                }
            }
            catch(e) {console.log("Создать объект XmlHttpRequest не удалось");};
            
        } else if (window.ActiveXObject) {
            //console.log("Создаю объект ActiveXObject...");
            try { 
                this.request = new ActiveXObject("Msxml2.XMLHTTP");
                if (typeof(this.request) == "object") {
                    console.log("Объект ActiveXObject \"Msxml2.XMLHTTP\"успешно создан");
                }
            }
            catch(e){console.log("Создать объект ActiveXObject(\"Msxml2.XMLHTTP\") не удалось");}
            try { 
                this.request = new ActiveXObject("Microsoft.XMLHTTP");
                if (typeof(this.request) == "object") {
                    console.log("Объект ActiveXObject \"Microsoft.XMLHTTP\"успешно создан");
                }
            }
            catch(e){console.log("Создать объект ActiveXObject(\"Microsoft.XMLHTTP\") не удалось");}
        } else {
            console.log("Создать объект \"XMLHttpRequest\" или \"ActiveXObject\" не удалось");
            this.request = null;
        }
    }

    /*
    пример AJAX запроса методом "GET"
    url - адрес запрашиваемой страницы
    method - false-синхронный, true-асинхронный
    */
    requestGet(url, method) {
        //запрос на сервер (false-синхронный, true-асинхронный)
        this.request.open("GET", url, method); //подготовка запроса
        //чтение ответа
        this.request.send(null);
        if (method) {
            this.currentMethod = "асинхронный";
            console.log(`Отправлен ${this.currentMethod} GET запрос по адресу: ${url}`);
            //console.log(this.request);

            //получаем состояние
            var that = this;
            this.request.onreadystatechange = function() {
                if (that.request.readyState == 4) {
                    if (that.request.status != 200) {
                        //добавляем ошибку
                        that.errors.push([that.request.status +": "+ that.request.statusText]);
                    }
                    //выводим статус ответа сервера
                    console.log("Статус ответа:");
                    console.log(that.request.status +": "+ that.request.statusText);

                    //Выводим дополнительную информацию
                    //that.request.getAllResponseHeader("Content-Length");
                    that.info = that.request.getAllResponseHeaders();
                    //console.log(`Доп.инфо: ${that.info}`);


                    that.serverAnswer = that.request.responseText;
                    //console.log("Передаю ответ в функцию \"showResultAsyncRequest\""); 
                    //that.showAnswerAsyncRequest(that.serverAnswer);

                    console.log("Результат:");
                    console.log("*** Start ***");
                    console.log(that.serverAnswer);
                    console.log("*** End ***");

                    that.showAsyncGetAnswer(that.serverAnswer);
                    //return;
                    //return that.serverAnswer;
                }
            }
        } else {
            this.currentMethod = "синхронный";
            console.log(`Отправлен ${this.currentMethod} запрос по адресу: ${url}`);

            this.serverAnswer = this.request.responseText;
            if (this.request.status != 200) {
                //добавляем ошибку
                this.errors.push([this.request.status +": "+ this.request.statusText]);
            }

            //выводим статус ответа
            console.log("Статус ответа:");
            console.log(this.request.status +": "+ this.request.statusText);

            //Выводим дополнительную информацию
            //that.request.getAllResponseHeader("Content-Length");
            this.info = this.request.getAllResponseHeaders();

            console.log("Результат:");
            console.log("*** Start ***");
            console.log(this.serverAnswer);
            console.log("*** End ***");
            return this.serverAnswer;
        }
        //вывод ошибок в консоль
        if (this.errors.length > 0) {
            console.log(`Ошибки: ${this.errors}`);
        } else {
            console.log(`Ошибки: отсутствуют`);
        }
    }

    /*
    пример AJAX запроса методом "POST"
    url - адрес запрашиваемой страницы
    method - false-синхронный, true-асинхронный
    searchString - отправляемые данные
    */
    requestPost(url, method, searchString) {
        //запрос на сервер (false-синхронный, true-асинхронный)
        this.request.open("POST", url, method); //подготовка запроса

        //установка заголовков
        //this.request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        this.request.setRequestHeader("Content-Type", "text/plain");
        //this.request.setRequestHeader("Content-Length", searchString.length);

        //отправка данных
        this.request.send(searchString);
        console.log(`Отправлено следующее: ${searchString} по адресу: ${url}`);
        if (method) {
            this.currentMethod = "асинхронный";
            console.log(`Отправлен ${this.currentMethod} запрос по адресу: ${url}`);
            //console.log(this.request);

            //получаем состояние
            var that = this;
            this.request.onreadystatechange = function() {
                if (that.request.readyState == 4) {
                    if (that.request.status != 200) {
                        //добавляем ошибку
                        that.errors.push([that.request.status +": "+ that.request.statusText]);
                    }
                    //выводим статус ответа сервера
                    console.log("Статус ответа:");
                    console.log(that.request.status +": "+ that.request.statusText);

                    //Выводим дополнительную информацию
                    //that.request.getAllResponseHeader("Content-Length");
                    that.info = that.request.getAllResponseHeaders();
                    console.log(`Доп.инфо: ${that.info}`);


                    that.serverAnswer = that.request.responseText;
                    console.log("Передаю ответ в функцию \"showAsyncPostAnswer\"");

                    console.log("Результат:");
                    console.log("*** Start ***");
                    console.log(that.serverAnswer);
                    console.log("*** End ***");

                    that.showAsyncPostAnswer(that.serverAnswer);
                    //return;
                    //return that.serverAnswer;
                }
            }
        } else {
            this.currentMethod = "синхронный";
            console.log(`Отправлен ${this.currentMethod} запрос по адресу: ${url}`);

            this.serverAnswer = this.request.responseText;
            if (this.request.status != 200) {
                //добавляем ошибку
                this.errors.push([this.request.status +": "+ this.request.statusText]);
            }

            //выводим статус ответа
            console.log("Статус ответа:");
            console.log(this.request.status +": "+ this.request.statusText);

            //Выводим дополнительную информацию
            //that.request.getAllResponseHeader("Content-Length");
            this.info = this.request.getAllResponseHeaders();

            console.log("Результат:");
            console.log("*** Start ***");
            console.log(this.serverAnswer);
            console.log("*** End ***");
            return;
        }
        //вывод ошибок в консоль
        if (this.errors.length > 0) {
            console.log(`Ошибки: ${this.errors}`);
        } else {
            console.log(`Ошибки: отсутствуют`);
        }
    }

    //вывод результата асинхронного GET запроса
    showAsyncGetAnswer(serverAnswer) {
        console.log("Метод showAsyncGetAnswer: измениете этот метод для обработкии вывода результата!!!");
    }

    //вывод результата асинхронного GET запроса
    showAsyncPostAnswer(serverAnswer) {
        console.log("Метод showAsyncPostAnswer: измениете этот метод для обработкии вывода результата!!!");
    }
}





//определение текущих размеров окна браузера
function  windowSize() {
    var height = window.innerHeight;
    var width = window.innerWidth;
    //var height = document.documentElement
    var windowSize = {};
    windowSize.width = width;
    windowSize.height = height;
    //console.log(windowSize);
    return windowSize;
}
//windowSize();


//генератор случайного цвета
function myColorGenerate() {
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

//функция выводит числа в диапазоне от start до end 
function myGetRangeNum(start, end) {
    // приводим к числам
    start = parseInt(start);
    end = parseInt(end);

    // проверка 1
    if ( isNaN(start) || isNaN(end) ) {
        console.log("Ошибка: пожалуйста введите число!");
        return false;
    }

    // проверка 2
    if (start > end) {
        console.log("Ошибка: неверно задан диапазон!");
        return false;
    }

    // выводим числа
    for (var i = start; i <= end; i++) {
        console.log(i);
    }
}

//getRangeNum(3,8);

//проверка чисел (чётные)
function myNumCheck(num) {
    if (num % 2) {
        return false
    }

    return true;
}

// вывод чётных чисел в заданном диапазоне
function getSimpleNum(start, end) {
    // приводим к числам
    start = parseInt(start);
    end = parseInt(end);

    // проверка 1
    if ( isNaN(start) || isNaN(end) ) {
        console.log("Ошибка: пожалуйста введите число!");
        return false;
    }

    // проверка 2
    if (start > end) {
        console.log("Ошибка: неверно задан диапазон!");
        return false;
    }

    //проверяем числа и выводим чётные
    for (var i = start; i <= end; i++) {
        if (i % 2) continue;

        console.log(i);
    }

    return true;
}

//getSimpleNum(1,10);

//выводит числа ряда Фибоначи в заданном диапазоне
function getFibonachi(start, end) {
    var a = 1;
    var b = 1;
    var number = 0; //номер по порядку числа Фибоначи

    for (i = 0; i <= end; i++) {

        var c = a + b;

                if (a == 1) {
                    number++;
                    if ( (number >= start) && (number <= end) ) {
                        console.log(a); document.write(a + " "); 
                    }

                    if (b == 2) {
                        number++;
                        if ( (number >= start) && (number <= end) ) {
                            console.log(b); document.write(b + " ");
                        }
                        number++;
                        if ( (number >= start) && (number <= end) ) {
                            console.log(c); document.write(c + " ");
                        }
                    }

                    a = b;
                    b = c;

                    continue;
                }

            number++;
            if ( (number >= start) && (number <= end) ) {
                console.log(c); document.write(c + " ");
            }
        
        a = b;
        b = c;

    }
}
//getFibonachi(1,17);

//округление до n знаков после запятой
//x - число, n - количество знаков
function myRoundPrimes(x, n) { 
    if (isNaN(x) || isNaN(n)) return false;
    var m = Math.pow(10,n);
    return Math.round(x*m)/m;
}


//удаление элемента по id
function myRemoveElemet(id) {
    if (this.checkElem(id)) {
        var elem = document.getElementById(id);
        elem.parentNode.removeChild(elem);//удаляем элемент
        var res = document.getElementById(id) == null ? true : false;
        console.log("результат удаления елемента: id=" + id + " - " + res);
        return res;
    }
}

//удаление содержимого элемента по id элемента
function myReplaseText(elemID, someText) {
    var elem = document.getElementById(elemID);
    elem.innerHTML = someText;//чистим поле
}

//проверка элемента на существование
//если существует - возвращает true, если нет -false
function myCheckElem(id) {
    return document.getElementById(id) != null ? true : false;
}

//структура "кольцевой массив"
//this - указатель на текущий объект; this - указывает на переменную
function myCircleArray(array) {

    this.mainArray = array;
    this.maxElementNumber = array.length - 1;
    console.log(`Максимальный индекс: ${this.maxElementNumber}`);

    //получить значение элемента по ключу (номеру элемента)
    this.getEl = function(elementNumber) {
        console.log(`Пришло: ${elementNumber}`);
        if (elementNumber > this.maxElementNumber) {
            newElementNumber = elementNumber - this.maxElementNumber - 1;
            console.log(`Изменили на: ${newElementNumber}`);
            //если новый (полученный) номер элемента всё ещё больше максимального - делаем рекурсию
            if (newElementNumber > this.maxElementNumber) {
                this.getEl(newElementNumber);
            } else {
                //return console.log(this.mainArray[newElementNumber]);
                console.log(`Вернули элемент номер: ${newElementNumber}`);
                console.log(`ЗНАЧЕНИЕ: ${this.mainArray[newElementNumber]}`);
                return this.mainArray[newElementNumber];
            }
            //если получили отрицательный номер элемента  
        } else if (elementNumber < 0) {
            newElementNumber = this.maxElementNumber + elementNumber + 1;

            //если новый (полученный) номер элемента всё ещё отрицательный - делаем рекурсию
            if (newElementNumber < 0) {
                this.getEl(newElementNumber);
            } else {
                //return console.log(this.mainArray[newElementNumber]);
                return this.mainArray[newElementNumber]
            }
            
            //если полученный номер элемента не привышает значение this.maxElementNumber
            //и не отрецацельный то выводим значение соответствующего элемента массива         
        } else {
            //return console.log(this.mainArray[elementNumber]);
            return this.mainArray[elementNumber];
        }
    }
}
/*
ПРИМЕРЫ:

расширение конструктора
Bird.prototype.setName = function(n) {
    this.name = n;
}
образец создания класса
class AjaxReq {
    constructor(n) {
        this.name = n;
    }
    someFunction() {
        console.log(this.name);
    }
}
// наследуется от AjaxReq
class SoftToys extends AjaxReq {
    constructor(n) {
        super(n);//вызовет конструктор у родителя
    }
}
*/