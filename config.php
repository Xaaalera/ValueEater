<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/roistat/ValueEater/ValueEater/HelpDataWorker.php';
HelpDataWorker::$KEY=''; //ключь от проекта посмотреть в кабинете ройстат в настройках интеграции с вашей CRM
HelpDataWorker::$projectId=''; //id проекта -  посмотреть в  адрессной строке ttps://cloud.roistat.com/projects/ТУТ НОМЕР ПРОЕКТВ/integration/proxyLeads
HelpDataWorker::$FormNameID=''; //ИД для имени формы создается в CRM
HelpDataWorker::$markerInputId=''; // ИД для маркера создается в CRM
HelpDataWorker::$checkDuplicate=0; // 1 емли нужна проверка на дубли
HelpDataWorker::$debug=0; //1 для включения вывода всех ошибок.
