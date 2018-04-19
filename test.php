<?php
/**
 * Created by PhpStorm.
 * User: xaaalera
 * Date: 13.03.18
 * Time: 9:38
 */

 //список всех методов ещё раз
ini_set('display_errors', 1); error_reporting(E_ALL);
require_once ('ValueEater/HelpDataWorker.php');
$roistat = new HelpDataWorker();
$roistat->getScriptAction();
$roistat->setCustomField($name, $value);
$roistat->getCustomfields();
$roistat->setTitle($name);
$roistat->getTitle();
$roistat->setFormName($name);
$roistat->getFormName();
$roistat->setRoistatMarker($name);
$roistat->getRoistatMarker();
$roistat->setRoistatVisit($name);
$roistat->getRoistatVisit();
$roistat->setComment($name);
$roistat->getComment();
$roistat->setEmail($name);
$roistat->getEmail();
$roistat->setName($name);
$roistat->getName();
$roistat->setPhone($name);
$roistat->getPhone();
$roistat->setCustomField(array('23'=>'123'),'123');
$roistat->sendToRoistat();

//пример вызова 
	$roistat = new HelpDataWorker($_REQUEST);
	$roistat->setFormName('Купить в 1 клик');
	$roistat->sendToRoistat();
	
