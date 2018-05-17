<?php

/**
 * Created by PhpStorm.
 * User: Xaalera
 * Date: 1/31/2018
 * Time: 7:48 PM
 */
class Data{
    protected $title, $customFields, $formName, $roistatVisit, $roistatMarker;
    protected $phone, $comment, $email, $name;
    
    public function __construct()
    {
        
        $this->roistatVisit  = isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : '';
        $this->roistatMarker = isset($_COOKIE['roistat_marker']) ? $_COOKIE['roistat_marker'] : 'Прямой заход';
        
    }
    
    public function getScriptAction()
    {
        
        return '{landingPage}';
    }
    
    public function setCustomField($name, $value)
    {
        try {
            $this->check('$name', $name);
            $this->customFields[$name] = $value;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
        
    }
    
    /**
     * @return array
     */
    public function getCustomfields()
    {
        return $this->customFields;
    }
    
    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        try {
            $this->check('$title', $title);
            $this->title = $title;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
    }
    
    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * @param mixed $formName
     */
    public function setFormName($formName)
    {
        try {
            $this->check('$formName', $formName);
            $this->formName = $formName;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
        
    }
    
    /**
     * @return mixed
     */
    public function getFormName()
    {
        return $this->formName;
    }
    
    /**
     * @return string
     */
    public function getRoistatMarker()
    {
        return $this->roistatMarker;
    }
    
    /**
     * @return string
     */
    public function getRoistatVisit()
    {
        return $this->roistatVisit;
    }
    
    /**
     * @param string $roistatVisit
     */
    
    public function setRoistatVisit($roistatVisit)
    {
        try {
            $this->checkNumber('$roistatVisit', $roistatVisit);
            $this->roistatVisit = $roistatVisit;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
    }
    
    
    /**
     * @param string $roistatMarker
     */
    public function setRoistatMarker($roistatMarker)
    {
        try {
            $this->checkNumber('$roistatMarker', $roistatMarker);
            $this->roistatMarker = $roistatMarker;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
    }
    
    public function getComment()
    {
        return $this->comment;
    }
    
    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $phone = preg_replace('/[^\d]/', '', $phone);
        $phone = preg_replace('/(^8)/', '7', $phone);
        
        try {
            if (empty($phone)) throw new Exception('Номер не должен быть пустым' . PHP_EOL);
            $this->phone = $phone;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
    }
    
    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        try {
            $this->check('$comment', $comment);
            $this->comment = $comment;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
    }
    
    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        try {
            if (empty($email)) throw new Exception('Емейл не должен быть пустым' . PHP_EOL);
            $this->email = $email;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
    }
    
    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        try {
            $this->check('$name', $name);
            $this->name = $name;
        }
        catch (Exception $e) {
            $this->printException($e);
        }
    }
    
    protected function check($nameValue, $Value)
    {
        if (empty($Value)) throw new Exception("{$nameValue} не должно быть пустым");
        elseif (!is_string($Value) && !is_numeric($Value)) throw new Exception("Ошибка {$nameValue} Вы передаете " . gettype($Value) . ' А должна быть строка!');
        
    }
    
    protected function checkNumber($nameValue, $Value){
        if (empty($Value)) throw new Exception("{$nameValue} не должно быть пустым");
        elseif (!is_numeric($Value)) throw new Exception("Ошибка {$nameValue} Вы передаете " . gettype($Value) . ' А должна быть строка!');
    }
    
    protected function printException(Exception $e)
    {
        echo "<pre>";
        print_r($e->getMessage());
        echo "</br>";
    }
    
    
    
}

