<?php

/**
 * Created by PhpStorm.
 * User: Xaalera
 * Date: 1/31/2018
 * Time: 5:20 PM
 */
require_once 'Data.php';
class HelpDataWorker extends Data
{
    
    const KEY = ''; //ключь нужен обязательно
    
    
    
    //вы можете использовать метод через константы а не облачный, достаточно заменить
    //метод $this->conversionValue($array); на $this->conversionValueInConstant($arrayValue);
    //в  конструкторе класса
    const NAMEARRAY  = array('name', 'fio', 'user_name');
    const PHONEARRAY = array('phone', 'telephone');
    const EMAILARRAY = array('email', 'mail', 'e-mail', 'user_mail', 'user_email');
    const TITLE      = array('title');
    const COMMENT    = array('comment', 'user_text', 'user_comment_area');
    const FORMNAME   = array('iblock_name');
    
    private $_phone, $_email, $_name, $_title, $_comment, $_formname,$path;
    
    public function __construct(array $array = array())
    {
        
        parent::__construct();
        $this->path = $_SERVER['DOCUMENT_ROOT'] . '/roistat' ;
        $this->getFileInfo(); //проверяем  есть ли файл , если есть то  берем с него инфу, если нет , то качаем и открываем.
        $this->conversionValue($array);
    }
    private function conversionValueInConstant($arrayValue)
    {
        if(!$arrayValue) return ;
        foreach ($arrayValue as $key => $value) {
            $key = strtolower($key);
            if(empty($value))continue;
            if (in_array($key, self::NAMEARRAY)) $this->setName($value);
            elseif (in_array($key, self::PHONEARRAY)) $this->setPhone($value);
            elseif (in_array($key, self::EMAILARRAY)) $this->setEmail($value);
            elseif (in_array($key, self::TITLE)) $this->setTitle($value);
            elseif (in_array($key, self::COMMENT)) $this->setComment($value);
            elseif (in_array($key, self::FORMNAME)) $this->setFormName($value);
        }
    }
    
    private function conversionValue($array)
    {
        
        if(!$array) return;
        foreach ($array as $key => $value) {
            $key = strtolower($key);
            if (empty($value)) continue;
            if (in_array($key, $this->_name)) $this->setName($value);
            elseif (in_array($key, $this->_phone)) $this->setPhone($value);
            elseif (in_array($key, $this->_email)) $this->setEmail($value);
            elseif (in_array($key, $this->_title)) $this->setTitle($value);
            elseif (in_array($key, $this->_comment)) $this->setComment($value);
            elseif (in_array($key, $this->_formname)) $this->setFormName($value);
        }
    }
    
    
    private function getCloudData()
    {
        $content               = file_get_contents('https://coolcodebro.ru/backjson.php');
        $content               = json_decode($content, 1);
        $content['date_write'] = date("Y-m-d H:i:s");
        if(!file_exists($this->path)){
            mkdir($this->path);
        }
        file_put_contents( $this->path. '/array.json', json_encode($content));
        $this->getFileJson();
        
    }
    
    private function getFileJson()
    {
        $dataJson = file_get_contents( $this->path.'/array.json');
        $dataJson = json_decode($dataJson);
        $this->setParamForConversion($dataJson);
//        $howMany  = strtotime(date("Y-m-d H:i:s")) - strtotime($dataJson->date_write) . PHP_EOL;
//        if ($howMany <= 10) $this->setParamForConversion($dataJson);
//        else $this->getCloudData();
    }
    
    private function setParamForConversion($data)
    {
        $this->_name     = $data->name;
        $this->_phone    = $data->phone;
        $this->_title    = $data->title;
        $this->_email    = $data->email;
        $this->_formname = $data->formname;
        $this->_comment  = $data->comment;
    }
    
    private function getFileInfo()
    {
        if (file_exists( $this->path.'/array.json')) $this->getFileJson();
        else $this->getCloudData();
    }
    
    
    public function sendToRoistat()
    {
        $roistatData = array('roistat' => $this->getRoistatVisit(),
                             'key'     => self::KEY,
                             'title'   => !empty($this->getTitle()) ? $this->getTitle() : "Заявка с формы {$this->getFormName()}",
                             'comment' => $this->getComment(),
                             'name'    => !empty($this->getName()) ? $this->getName() : 'неизвестный контакт',
                             'email'   => $this->getEmail(),
                             'phone'   => $this->getPhone(),
                             'fields'  => $this->getCustomfields());
        file_get_contents("https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));
        
    }
    
}