<?php
class EJTimeline extends CWidget
{
    /** 
     * @var string the class name of the model.
     * @since 0.1
     */
    public $modelName; 
    /** 
     * @var string the attibute name containing a timestamp.
     * @since 0.1
     */
    public $attribute; 
    /** 
     * @var string the group by condition. example: 'to_days(tl_timestamp)' if you want per day.    
     * @since 0.1
     */
    public $groupby;
    /**
     * @var string the format of the header part. will be used to date function as 1st param.
     */
    public $headerFormat = "d/m/Y"; 
    /** 
     * @var array config array that will be used to in a CActiveDataProvider to retrieve all event sets.
     */
    public $CActiveDataProviderConfig = array();
    /** 
     * @var array config array that will be used in the CListView rendering events.    
     */
    public $CListViewConfig = array(
        'itemView'=>'_view',
    );
    /** 
     * @var boolean if the model attribute representing the time is a unixtimestamp.
     */
    public $unixepoch = false;
    /** 
     * @var array the core scripts to register.
     * @since 0.1
     */
    private $core = array('jquery', 'jquery.ui');
    /**
     * @var array css files to register.
     * @since 0.1
     */
    private $css = array('ejtimeline.css');
    /**
     * @var array the js files to register.
     * @since 0.1
     */ 
    private $js  = array('ejtimeline.js');
    /** 
     * @var string The asset folder after published.
     * @since 0.1
     */
    private $assets;
    /** 
     * @var array all the properties of this widget that must be set.
     * @since 0.1
     */
    private $issetProperties = array(
        'modelName', 'attribute','groupby'
    );
    /** 
     * @var CDbCriteria the criteria for event CActiveDataProviders.
     */
    private $criteria;
    /** 
     * @var arrray key=>value array where key is the header and value is the dataprovider for the event.
     */
    private $events = array();

    public function init()
    {
        $this->checkProperties();
        $this->registerScripts();     

        if(isset($this->CActiveDataProviderConfig['criteria'])) {
            $c = $this->CActiveDataProviderConfig['criteria'];
            $this->criteria = ($c instanceof CDbCriteria) ? $c : new CDbCriteria($c);
        } else { 
            $this->criteria = new CDbCriteria();
        }
        $model = new $$this->modelName();
        $periods = $model->findAll(array(
            'group'=>$this->groupby,
            'select'=>$this->attribute,
        ));
        
        foreach($periods as $m)
        {
            $this->CActiveDataProviderConfig['criteria'] = clone($this->criteria);
            $this->CActiveDataProviderConfig['criteria']->compare($this->attribute, $m->{$this->attribute});
            
            $strtotime = ($this->unixepoch) ? $m->{$this->attribute} : strtotime($m->{$this->attribute});
            $h = date($this->headerFormat, $strtotime);
            $this->events[$h] = new CActiveDataProvider($this->modelName, $this->CActiveDataProviderConfig);
        }
        parent::init();
    }
    public function run()
    {
        if(!empty($this->events)) {
            $this->render('index', array(
                'events'=>$this->events, 'config'=>$this->CListViewConfig
            ));
        }
    }
    private function checkProperties()
    {
        foreach($this->issetProperties as $p)
        {
            if(!isset($this->$p)) 
                throw new CException($p." property must be set.");
        }
    }
    private function registerScripts()
    {
        $cs = Yii::app()->clientScript;
        $assets = dirname(__FILE__).DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR;
        $this->assets = Yii::app()->getAssetManager()->publish($assets);

        foreach($this->css as $file)
        {
            $cs->registerCssFile($this->assets."/".$file);
        }
        foreach($this->core as $file) 
        {
            if(!$cs->isScriptRegistered($file)) {
                $cs->registerCoreScript($file);
            }
        }
        foreach($this->js as $file)
        {
            $cs->registerScriptFile($this->assets."/".$file, CClientScript::POS_END);
        }
    }
}
