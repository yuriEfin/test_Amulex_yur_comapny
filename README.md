Задание: 

"Эмулировать работу пользователя в браузере" - авторизоваться
средствами PHP в Яндекс.Почта, получить ключ авторизации и  с его помощью вернуть json массив с данными (от кого письмо Email, от кого
письмо Заголовок, Заголовок письма, Время получения).
Условия:
- без использования IMAP, POP, API Яндекса;
- один файл;
- ООП
------------------------------------------------------------

Решение через Curl + Xpath

------------------------------------------------------------

Нужно ввести на странице данные для авторизации - далее скрипт выведет в первой части экрана - json (через **'echo'**)

Ниже распечатаный массив (** print_r **) - для наглядности

