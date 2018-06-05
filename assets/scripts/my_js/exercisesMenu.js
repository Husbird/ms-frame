/**
 * Created by User on 18.04.2017.
 */
console.log("exercisesMenu.js успешно подключён ...");
window.addEventListener("DOMContentLoaded", exercisesMenuInit);

function clickAddExPart() {
    getPage('AddExPartForm'); //получаем контенет страницы
}

function clickAddExMain() {
    getPage('AddExMainForm'); //получаем контенет страницы
}

function clickAddExercise() {
    getPage('AddExercise'); //получаем контенет страницы
}

function exercisesMenuInit() {
    "use strict"
    console.log("запускаю функцию exercisesMenuInit ...");

    //карусель
    $('.carousel').carousel();
}
