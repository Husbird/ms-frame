/**
 * Created by MSS on 29.10.2017.
 */
 window.addEventListener("DOMContentLoaded", tagParse);
//скрипты текущего приложения
console.log("tagParser.js успешно подключён");

//парсим квадратные скобки в тексте:
function tagParse() {
        var textArr = [];
        var textElem = document.getElementById("mainPageText");
        var text = textElem.innerHTML;
        textArr = text.split("");
        charCount = textArr.length;

        var mixedTextArr = textArr.map(function(item) {
            
            switch(item) {
                case "[":
                    item = "<";
                    break;
                case "]":
                    item = ">";
                    break;
                default:
                    break;
            }
            return item;
        });
        mixedTextStr = mixedTextArr.join("");
        myReplaseText("mainPageText", mixedTextStr);


}