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
    
    public static $KEY; //ключь нужен обязательно
    public static $markerInputId; //маркерИД если вы планируете его передавать
    public static $FormNameID; ///FormNameID если вы планируете передавать  название формы
    public static $checkDuplicate; //проверка на дубли если нужна выставить 1
    public static $projectId; //устанавливаем ProjectID нужно для событий  ;
    public static $debug ; //если 1  - то включит отображение всех ошибок,  пользуйтесь аккуратно, все может вылезти наружу.
    
    
    
    //вы можете использовать метод через константы а не облачный, достаточно заменить
    //метод $this->conversionValue($array); на $this->conversionValueInConstant($arrayValue);
    //в  конструкторе класса
//    const NAMEARRAY  = array('name', 'fio', 'user_name');
//    const PHONEARRAY = array('phone', 'telephone');
//    const EMAILARRAY = array('email', 'mail', 'e-mail', 'user_mail', 'user_email');
//    const TITLE      = array('title');
//    const COMMENT    = array('comment', 'user_text', 'user_comment_area');
//    const FORMNAME   = array('iblock_name');
//
    private $_phone, $_email, $_name, $_title, $_comment, $_formname,$path;
    
    public function __construct(array $array = array())
    {
        
        parent::__construct();
        $this->path = $_SERVER['DOCUMENT_ROOT'] . '/roistat' ;
        $this->debug();
        if($array != array()){
            $this->getFileInfo(); //проверяем  есть ли файл , если есть то  берем с него инфу, если нет , то качаем и открываем.
            $this->conversionValue($array);
        }
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
        $content               = $this->SendRequest('https://coolcodebro.ru/backjson.php');
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
        $dataJson = file_get_contents($this->path.'/array.json');
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
    
    
    private  function SetRoistatAddFields(){
        $formID =HelpDataWorker::$FormNameID;
        $marlerid=HelpDataWorker::$markerInputId;
        if(mb_strlen($formID) != 0){
            $this->setCustomField($formID,$this->getFormName());
        }
        if(mb_strlen($marlerid) != 0) {
            $this->setCustomField($marlerid, $this->getRoistatMarker());
        }
    }
    
    public function sendToRoistat()
    {
        $this->SetRoistatAddFields();
        
        $roistatData = array('roistat' => $this->getRoistatVisit(),
                             'key'     => HelpDataWorker::$KEY,
                             'title'   => !empty($this->getTitle()) ? $this->getTitle() : "Заявка с формы {$this->getFormName()}",
                             'comment' => $this->getComment(),
                             'name'    => !empty($this->getName()) ? $this->getName() : 'неизвестный контакт',
                             'email'   => $this->getEmail(),
                             'phone'   => $this->getPhone(),
                             'fields'  => $this->getCustomfields());
        
        $duplicate = 0;
        $result = 'Duplicate';
        if(HelpDataWorker::$checkDuplicate){
            $duplicate=   $this->checkDuplicate($roistatData);
        }
        if(!$duplicate) {
             $url = "https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData);
            $result= $this->SendRequest($url);
        }
        return $result;
    }
    public function checkDuplicate($array){
        $arrayCheck=sha1(md5(json_encode($array)));
        $issetsArrayInCookie = isset($_COOKIE['roistat_help_entity']) ? $_COOKIE['roistat_help_entity'] : '' ;
        if(mb_strlen($issetsArrayInCookie) == 0 ){
            setcookie('roistat_help_entity',$arrayCheck,time()+300);
            return 0;
        }
        elseif($arrayCheck == $issetsArrayInCookie){
            return 1;
        }
        else{
            return 0;
        }
    }
    
    //get
    public function SendRequest($url){
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        return $contents;
    }
    
    public  function sendEventToRoistat($eventName){
        $projectID = HelpDataWorker::$projectId;
        $url =  "https://cloud.roistat.com/api/site/1.0/{$projectID}/event/register?event={$eventName}&visit={$this->getRoistatVisit()}&data[page]={$_SERVER['HTTP_REFERER']}&data[domain]={$_SERVER['SERVER_NAME']}";
        $reust  = $this->SendRequest($url);
        return $reust;
    }
    
    private  function debug(){
        if(!HelpDataWorker::$debug) {
            return ;
        }
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
    
    
    
}
