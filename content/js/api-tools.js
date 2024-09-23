//
// Библиотека функций для выполнения API запросов
//

//---------------------------------------------------
// вывод запроса и результата при отладке (глобально)
// для включения в отдельном файле необходимо переопределить:
// showDebugInfo = true;
//---------------------------------------------------
let showDebugInfo = false;

/**
 * Функция выполнения api-запроса
 *
 * @param {*} requestType Метод запроса (POST, GET, FILE и т.п.)
 * @param {*} apiUrl URL запроса
 * @param {*} successFunc Функция обработки результата
 * @param {*} requestData Массив отправляемых данных
 */
function apiRequest(requestType, apiUrl, successFunc, requestData = "") {
    let startTime = performance.now();
    // Вывод сообщения о выполнении запроса.
    $("#api-waiting").css("visibility", "visible");
    requestInfo = "url: " + apiUrl + "<br>method: " + requestType + "<br>function: " + successFunc.name
        + "<br>data: " + requestData;
    alertInfo = "url: " + apiUrl + "\nmethod: " + requestType + "\nfunction: " + successFunc.name
        + "\ndata: " + requestData;
    //alert(alertInfo);

    // Параметры запроса
    $ajaxParams = {
        url: apiUrl,
        data: requestData,
        dataType: "json",
        success: function (result) {
            //successFunc(result);              // если не нужна предварительная обработка ответа
            apiParsing(result, successFunc);    // для предварительной обработки ответа
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //alert("Ошибка на стороне сервера");
            $("#api-waiting").css("visibility", "hidden");
            $("#api-message-text").html("<h2 class='red'>Ошибка на стороне сервера</h2>Обратитесь к администратору сайта.");
            $("#api-message").css("visibility", "visible");
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    };
    if (requestType == "FILE") {
        $ajaxParams["method"] = "POST";
        $ajaxParams["cache"] = false;
        $ajaxParams["contentType"] = false;
		$ajaxParams["processData"] = false;
    } else {
        $ajaxParams["method"] = requestType;
        $ajaxParams["contentType"] = "application/json";
    }

    // Выполнение запроса
    $.ajax($ajaxParams)
        .done(function (result) {
            // вывод запроса и результата (для отладки)
            if (showDebugInfo) {
                let endTime = performance.now();
                console.log(result);
                console.log("Время выполнения запроса: " + (endTime - startTime) + " ms");
                $("#api-debug-info").html(
                    "<div class=\"debug-info\">Запрос:<br>" + requestInfo + "<br>--- success ---</div>" +
                    "<div class=\"debug-info\">Результат (успешно):<br>" + JSON.stringify(result) + "</div>" +
                    "<div class=\"debug-info\">Время выполнения запроса: " + (endTime - startTime) + " ms</div>"
                );
            } else {
                $("#api-debug-info").html("");
            }
        })
        .fail(function (result) {
            // вывод запроса и результата (для отладки).
            if (showDebugInfo) {
                $("#api-debug-info").html(
                    "<div class=\"debug-info\">Запрос:<br>" + requestInfo + "<br>--- success ---</div>"
                    + "<div class=\"debug-info\">Результат (ошибка):<br>" + JSON.stringify(result) + "</div>"
                );
            } else {
                $("#api-debug-info").html("");
            }
        })
        .always(function (result) {
            $("#api-waiting").css("visibility", "hidden");
});
}

//---------------------------------------------------
// функция обработки ответа api-запроса apiRequest()
//---------------------------------------------------
function apiParsing(result, successFunc) {
    if ( isSet(result["errors"]) && result["errors"].length > 0) {
        text = "<h2 class='red'>При выполнении скрипта выявлены ошибки:</h2>";
        text += result["errors"].join('<br>');
        $("#api-message-text").html(text);
        $("#api-message").css("visibility", "visible");
    }
    // успешный ответ должен быть в виде массива ["status" => "ok", "data" => <данные>]
    switch (result["status"]) {
        case "ok":
            // передача полученных данных пользовательской функции
            successFunc(result["data"]);
            break;
        case "warning":
            alert(result["info"]);
            break;
        case "error":
            alert('Ошибка на стороне сервера:\n' + result["info"] + '\nОбратитесь к администратору сайта.');
            break;
        default:
            alert(result["data"]);
            break;
    }
}

//---------------------------------------------------
// добавление вспомогательных информационных блоков
//---------------------------------------------------
$(document).ready(function() {
    // Блок для вывода запроса и результата (для отладки)
    $("body").append("<div id='api-debug-info'></div>");
    // Блок для вывода сообщения о выполнении запроса.
    $("body").append("<div class='api-info' id='api-waiting'><div>Выполняется запрос...</div></div>");
    // Блок для вывода сообщений о результатах запроса.
    $("body").append("<div class='api-info' id='api-message'><div><div id='api-message-text'></div><br><button id='btn-api-info'>Закрыть</button></div></div>");

    // кнопка закрытия окна информации
    $("#btn-api-info").on('click', function() {
        $("#api-message").css("visibility", "hidden");
    });
    // --- Стили блоков ---
    // серый фон на весь экран
    $(".api-info").css({
        "position": "fixed",
        "top": "0",
        "left": "0",
        "width": "100%",
        "height": "100%",
        "background-color": "rgba(136, 136, 136, 0.9)",
        "z-index": "999999",
        "visibility": "hidden",
    });
    // сообщение о выполнении запроса
    $("#api-waiting").children("div").css({
        "width": "200px",
        "padding": "20px",
        "background-color": "#ffffe8",
        "color": "red",
        "font-weight": "bold",
        "text-align": "center",
        /* размещение по центру экрана */
        "position": "fixed",
        "top": "50%",
        "left": "50%",
        "transform": "translate(-50%, -50%)",
    });
    // вывод предупреждений и сообщений об ошибках
    $("#api-message").children("div").css({
        "width": "600px",
        "padding": "20px",
        "background-color": "#fff",
        "text-align": "center",
        /* размещение по центру экрана */
        "position": "fixed",
        "top": "50%",
        "left": "50%",
        "transform": "translate(-50%, -50%)",
    });

    //$("#api-info").css("visibility", "visible");

});

//---------------------------------------------------
// Вспомогательные утилиты
//---------------------------------------------------

function nl2br(str, is_xhtml) {
    // Заменяет перевод строки на тег <br>
    var breakTag = (is_xhtml || typeof is_xhtml === "undefined") ? "<br " + "/>" : "<br>"; // Adjust comment to avoid issue on phpjs.org display
    return (str + "").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, "$1" + breakTag + "$2");
}

function fillZero(str, format = "00") {
    // Дополняет строку начальными нулями по заданному формату
    return (format + str).slice(- format.length);
}

function unixtime2str(timestamp = 0, withTime = false) {
    // Преобразует метку времени в строку формата "дд.мм.гггг чч:мм"
    // Класс Date оперирует миллисекундами
    // amoCRM возвращает timestamp в секундах
    let date = new Date();
    if (timestamp > 0) {
        //date.setTime(timestamp * 1000);
        date.setTime(timestamp * 1000);
    }
    str = fillZero(date.getDate()) + "." + fillZero(date.getMonth() + 1) + "." + date.getFullYear();
    if (withTime) {
        str += " " + fillZero(date.getHours()) + ":" + fillZero(date.getMinutes());
    }
    return str;
}

function isSet(el) {
    // Проверка существования переменной
    if (typeof (el) != "undefined" && el !== null) {
        return true;
    } else {
        return false;
    }
}

function ifIsSet(el, def = "") {
    // Проверка существования переменной
    if (typeof (el) != "undefined" && el !== null) {
        return el;
    } else {
        return def;
    }
}

function array2table(array, table_class = "") {
    // вывод массива в виде html-таблицы
    text = "<table class=\"" + table_class + "\">";
    //array.forEach не работает с ассоциативными массивами
    for (ii = 0; ii < array.length; ++ii) {
        text += "<tr>";
        for (jj = 0; jj < array[ii].length; ++jj) {
            text += "<td>" + array[ii][jj] + "</td>";
        }
        text += "</tr>";
    }
    text += "</table>";
    return text;
}
