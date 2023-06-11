<?php 

$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

// функция возвращает полное имя из трёх слагаемых
function getFullnameFromParts ($surname, $name, $patronomyc) {
    return $surname." ".$name." ".$patronomyc;
}

// функция вовращает массив из трёх элементов имени
function getPartsFromFullname ($fullname) {
    $a = ["surname", "name", "patronomyc"];
    return array_combine ($a, explode (" ", $fullname));
}

// функция возвращает имя, первую букву фамилии и точку
function getShortName ($fullname) {
    $array = getPartsFromFullname($fullname);
    return $array["name"]." ".mb_substr ($array["surname"], 0, 1).".";
}

// функция определяет пол по имени
function getGenderFromName ($fullname) {
    $array = getPartsFromFullname($fullname);
    $gender = 0;
    if (mb_substr ($array["patronomyc"], -3, 3) === "вна") $gender --;       // анализ на признаки женского пола
    if (mb_substr ($array["name"], -1, 1) === "а") $gender --;
    if (mb_substr ($array["surname"], -2, 2) === "ва") $gender --;
    
    if (mb_substr ($array["patronomyc"], -2, 2) === "ич") $gender ++;       // анализ на признаки мужского пола
    if (mb_substr ($array["name"], -1, 1) === "й" || mb_substr ($array["name"], -1, 1) === "н") $gender ++;
    if (mb_substr ($array["surname"], -1, 1) === "в") $gender ++;

    if ($gender > 0) { return 1;            //если пол мужской вовращет 1
    } elseif ($gender < 0) { return -1;     //если пол женский вовращет -1
    } elseif ($gender == 0) {return 0;      //если пол определить невозможно вовращет 0
    }          
}

// функция анализирует входящий массив по полу
function getGenderDescription ($array) {
    $total = count ($array);

    $male_count = count (array_filter($array, function($value) {            // анализ всего массива на признаки мужского пола
        return getGenderFromName ($value["fullname"]) == 1;
    }));

    $female_count = count (array_filter($array, function($value) {             // анализ всего массива на признаки женского пола
        return getGenderFromName ($value["fullname"]) == -1;
    }));

    $unknown_count = count (array_filter($array, function($value) {             // анализ всего массива на неопределённый пол
        return getGenderFromName ($value["fullname"]) == 0;
    }));

    $total = count ($array);                                                    //вычисление процентов по каждом полу
    $male = round ($male_count*100/$total, 1);
    $female = round ($female_count*100/$total, 1);
    $unknown = round ($unknown_count*100/$total, 1);

    echo( 
    <<<MYHEREDOCTEXT
    Гендерный состав аудитории:
    _ _ _ _ _ _ _ _ _ _ _ _ _ _ 

    Мужчины - $male%
    Женщины - $female%
    Не удалось определить - $unknown%
    MYHEREDOCTEXT
    );
}

// функция подбора идеальной пары. Принимает на входе фамилию, имя, отчество и массив
// возвращает случайно подобранного пользователя противоположного пола 
function getPerfectPartner ($surname, $name, $patronomyc, $array) {
    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);         //приводим ФИО к заглавной первой букве
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronomyc = mb_convert_case($patronomyc, MB_CASE_TITLE_SIMPLE);

    $fullname = getFullnameFromParts ($surname, $name, $patronomyc);    //склеиваем ФИО в одну строку
    $genderNew = getGenderFromName ($fullname);                          // определяем пол     
    $temp = rand(0, count($array)-1);                                   
    $genderArray = getGenderFromName ($array[$temp]['fullname']);        // выбираем случайного пользователя из массива

    $fullnameShort = getShortName ($fullname);                          // приводим ФИО к короткой форме
    $ArraynameShort = getShortName ($array[$temp]['fullname']);         

    $temp = rand(5000, 10000) / 100;                                     // процент совместимости - случайное число
    if ($genderNew == 0) {echo "К сожалению, пару не удалось подобрать, введите ещё раз";
    } elseif ($genderNew * $genderArray == -1) { 
        print_r ("$fullnameShort + ".$ArraynameShort." =\n") ;                  // вывод идеальной пары
        print_r (mb_chr(9825, 'UTF-8'). " Идеально на $temp % ". mb_chr(9825, 'UTF-8'));
        } elseif ($genderNew * $genderArray == 1 || $genderArray == 0 ) {       // если пол совпал, то повторный поиск
            getPerfectPartner ($surname, $name, $patronomyc, $array);}
}; 

//Раскомментировать для запуска getPerfectPartner
//   $surname2 = "Иванов";
 //   $name2 = "Иван";
 //   $patronomyc2 = "Иванович";

 //   getPerfectPartner ($surname2, $name2, $patronomyc2, $example_persons_array);
   