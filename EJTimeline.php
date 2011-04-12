<?php
class EJTimeline extends CWidget
{
    /**  The model class name.
     * @var string
     * @since 0.1
     */
    public $modelName; 
    /** Attriubte name containing date data. 
     * @var string
     * @since 0.1
     */
    public $attribute; 
    /** SQL group condition for events grouping. Example: 'to_days(tl_timestamp)' for per day group.
     * @var string
     * @since 0.1
     */
    public $groupby;
    /** Format of the header display. Will be used in the php date 1st param.
     * @var string
     * @since 0.1
     */
    public $headerFormat = "d/m/Y"; 
    /** Config array for the events dataprovider of each period.
     * @var array
     * @since 0.1
     */
    public $CActiveDataProviderConfig = array();
    /** CListView configuration for rendering the events.     
     * @var array
     * @since 0.1
     */
    public $CListViewConfig = array(
        'itemView'=>'_view',
    );
    /** If attribute is in unixstamp format.
     * @var boolean
     * @since 0.1
     */
    public $unixepoch = false;
    /** Core scripts to register, if not already.
     * @var array
     * @since 0.1
     */
    private $core = array('jquery', 'jquery.ui');
    /** Css files to register
     * @var array
     * @since 0.1
     */
    private $css = array('ejtimeline.css');
    /** Js files to register.
     * @var array
     * @since 0.1
     */ 
    private $js  = array('ejtimeline.js');
    /** The asset folder after published.
     * @var string
     * @since 0.1
     */
    private $assets;
    /** All properties that are required to be set.
     * @var array
     * @since 0.1
     */
    private $issetProperties = array(
        'modelName', 'attribute','groupby'
    );
    /** Criteria for the CActiveDataProviders.
     * @var CDbCriteria
     */
    private $criteria;
    /**  'Key' => 'value' array. Key is the header and value is the dataprovider containt the events.
     * @var arrray
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
        $m = $this->modelName;
        $model = new $m();

        $tableName = $model->tableName();

        $periods = Yii::app()->db->createCommand()
            ->select('distinct '.$this->groupby)
            ->from($tableName)
            ->queryAll();
        
        foreach($periods as $m)
        {
            $t = $m[$this->groupby];
            $this->CActiveDataProviderConfig['criteria'] = clone($this->criteria);
            $this->CActiveDataProviderConfig['criteria']->addCondition($this->groupby." = '".$m[$this->groupby]."'");
            
            $h = date($this->headerFormat, strtotime($t));
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
