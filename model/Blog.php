<?php

/**
 * This is the model class for table "{{blog}}".
 *
 * The followings are the available columns in table '{{blog}}':
 * @property integer $blog_id 
 * @property integer $blogcategories_id
 * @property string $title
 * @property string $page_address_identifier
 * @property string $description
 * @property string $image
 * @property string $image_name_date
 * @property string $author
 * @property string $publish_date
 * @property string $client_ip
 * @property string $status
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $createdon
 * @property string $updatedon
 */
class Blog extends Security {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Blog the static model class
     */
    public $image; // used by the form to send the file. 

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{blog}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('blogcategories_id,title,description', 'required', 'message' => CommonFunctions::getText(MSG_ATTRIBUTE_BLANK)),
            array('author', 'required', 'message' => CommonFunctions::getText(MSG_ATTRIBUTE_BLANK), 'on' => 'bloguser'),
            array('author', 'length', 'max' => 100, 'on' => 'bloguser'),
            array('profile_url', 'url', 'allowEmpty' => true, 'defaultScheme' => 'http', 'message' => CommonFunctions::getText(MSG_BLOG_URL_INVALID)),
            //array('image', 'file', 'types' => Yii::app()->params['blogImageTypes'], 'allowEmpty' => true, 'maxSize' => Yii::app()->params['blogImageMaxSize'], 'tooLarge' => CommonFunctions::getText(MSG_BLOG_IMAGE_INVALID_SIZE), 'wrongType' => CommonFunctions::getText(MSG_BLOG_IMAGE_INVALID)),
            array('image','file','allowEmpty'=>true,
                  'types'=>Yii::app()->params['blogImageTypes'],
                 'on'=>'insert',
                 'except'=>'update',
                'message'=>CommonFunctions::getText(MSG_BLOG_IMAGE_INVALID),
                'wrongType'=>CommonFunctions::getText(MSG_BLOG_IMAGE_INVALID),
                'maxSize'=>Yii::app()->params['blogImageMaxSize'],
                'tooLarge' => CommonFunctions::getText(MSG_BLOG_IMAGE_INVALID_SIZE),
                ),
            
            array('author,blogcategories_id,publish_date,image,status,image_name_date, meta_title, meta_keywords, meta_description', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('blog_id, title, page_address_identifier, description, image, image_name_date,author,publish_date,client_ip, status, createdon, updatedon, meta_title, meta_keywords, meta_description', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'bc' => array(self::HAS_MANY, 'BlogComments', 'blog_id'),
            'bcat' => array(self::BELONGS_TO, 'Blogcategories', 'blogcategories_id'),
                //'buser' => array(self::BELONGS_TO, 'User', 'author'),
        );
    }

    /**
     * behavior for image attributes.
     */
    public function behaviors() {
        return array(
            'blogImgBehavior' => array(
                'class' => 'ImageARBehavior',
                'attribute' => 'image', // this must exist
                'attributeForName' => 'image',
                'extension' => Yii::app()->params['blogImageExt'], // possible extensions, comma separated
                'prefix' => Yii::app()->params['blogImagePrefix'],
                'relativeWebRootFolder' => Yii::app()->params['blogImagePath'], // this folder must exist
                //'_fileName' => date('YmdHis'),
                'forceExt' => null, // this is the default, every saved image will be a png one.
                # Set to null if you want to keep the original format
                'processor' => 'ext.image.Image',
                'formats' => array(
                    // create a thumbnail grayscale format
                    'thumb' => array(
                        'suffix' => Yii::app()->params['blogImageThumbSuffix'],
                        'process' => array('resizecrop' => array(Yii::app()->params['blogThumbImageWidth'], Yii::app()->params['blogThumbImageHeight'])),
                    ),
                ),
            ),
            'ERememberFiltersBehavior' => array(
                'class' => 'application.components.ERememberFiltersBehavior',
                'defaults' => array(), /* optional line */
                'defaultStickOnClear' => false /* optional line */
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'blog_id' => CommonFunctions::getText(LBL_BLOG_ID),
            'blogcategories_id' => CommonFunctions::getText(LBL_BLOGCATEGORIES_ID),
            'title' => CommonFunctions::getText(LBL_BLOG_TITLE),
            'description' => CommonFunctions::getText(LBL_BLOG_DESCRIPTION),
            'image' => CommonFunctions::getText(LBL_BLOG_IMAGE),
            'author' => CommonFunctions::getText(LBL_BLOG_AUTHOR),
			'profile_url' => CommonFunctions::getText(LBL_BLOG_PROFILE),
            'publish_date' => CommonFunctions::getText(LBL_BLOG_PUBLISH_DATE),
            'status' => CommonFunctions::getText(LBL_BLOG_STATUS),
            'meta_title' => CommonFunctions::getText(LBL_META_TITLE),
            'meta_keywords' => CommonFunctions::getText(LBL_META_KEYWORDS),
            'meta_description' => CommonFunctions::getText(LBL_META_DESCRIPTION),
            'createdon' => CommonFunctions::getText(LBL_BLOG_CREATED),
            'updatedon' => CommonFunctions::getText(LBL_BLOG_UPDATED),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('t.title', CommonFunctions::escapeOperator($this->title), true, 'OR');
        $criteria->compare('t.description', CommonFunctions::escapeOperator($this->title), true, 'OR');
        $criteria->compare('t.status', $this->status);
        $criteria->compare('blogcategories_id', $this->blogcategories_id);
        $criteria->with = 'bcat';
        $criteria->together = true;
        $sort = new CSort;
        $sort->defaultOrder = array('title' => CSort::SORT_ASC); //'name ASC';
        $sort->attributes = array(
            'title' => 't.title',
            'status' => 't.status',
            'author' => 'author',
            'publish_date' => 'publish_date',
            'bcat.title' => 'bcat.title',
        );
        $sort->applyOrder($criteria);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
            'pagination' => array(
                // This line below is the only addition made to the model by Page size dropdown.
                'pageSize' => Yii::app()->user->getState('blogPageSize', Yii::app()->params['RECORDPERPAGE_ADMIN']),
                // This line below is the only addition made to the model in
                // choosing the page to be displayed.
                'currentPage' => Yii::app()->user->getState('Blog_page', 0),
            ),
        ));
    }

    public function onBeforeValidate($event) {
        $description = trim($this->description);
        $description = str_replace('&nbsp;', '', $description);
        $description = str_replace('<br>', '', $description);
        $description = str_replace('<br />', '', $description);
        $description = str_replace('\r', '', $description);
        $description = str_replace('\n', '', $description);        
        if (trim($description) == "") {
            $this->addError('description', CommonFunctions::getText(MSG_BLOG_DESC_EMPTY));
        }
        //return parent::beforeValidate();
    }

    public function afterSave() {
        if (Yii::app()->params['ajaxFileUpload']) {
            $this->updateFile();
            parent::afterSave();
        } else {
            parent::afterSave();
        }
    }

    public function updateFile() {
        if (Yii::app()->user->hasState(Yii::app()->params['blogajaxImageVar'])) {
            $src_filepath = Yii::app()->user->getState(Yii::app()->params['blogajaxImageVar']);
            $src_filename = substr($src_filepath, strrpos($src_filepath, "/") + 1);
            $src_ext = CommonFunctions::getFileExtension($src_filename);
            
            $time_stamp = CommonFunctions::getTimeStamp() . '_';

            $dest_filepath = Yii::app()->params['blogImagePath'];
            $dest_filename = Yii::app()->params['blogImagePrefix'] . $time_stamp . $this->blog_id; //.'.'.$ext;
            $dest_filenameext = $dest_filename . '.' . $src_ext;

            if (file_exists(Yii::app()->user->getState(Yii::app()->params['blogajaxImageVar'])) && !is_dir(Yii::app()->user->getState(Yii::app()->params['blogajaxImageVar']))) {
                if ($this->image <> '') {
                    CommonFunctions::deleteFile(Yii::app()->params['blogImagePath'], $this->image, true);
                }
                copy(Yii::app()->user->getState(Yii::app()->params['blogajaxImageVar']), $dest_filepath . '/' . $dest_filenameext);
                CommonFunctions::updateFileField($dest_filenameext, $this->blog_id, 'blog_id', 'image', $this->tableName(), 'image_name_date', $src_filename);
                CommonFunctions::optimizedImage($dest_filepath,$dest_filenameext);
                Yii::import('ext.image.Image');
                $processor = new Image($dest_filepath . '/' . $dest_filenameext);
                $this->process($processor, array('resizecrop' => array(Yii::app()->params['blogThumbImageWidth'], Yii::app()->params['blogThumbImageHeight'])));
                $processor->save($dest_filepath . '/' . Yii::app()->params['blogImagePrefix'] . $time_stamp . $this->blog_id . Yii::app()->params['blogImageThumbSuffix'] . '.' . $src_ext);

                unlink(Yii::app()->user->getState(Yii::app()->params['blogajaxImageVar']));
                Yii::app()->user->setState(Yii::app()->params['blogajaxImageVar'], null);
            }
        }
    }

    public function getSearchCriteria() {
        $criteria = new CDbCriteria;
        $criteria->with = 'bc';
        $criteria->condition = 't.status = :status AND t.publish_date<=CURDATE() AND (t.title LIKE :title OR t.description like :description)';
        $criteria->params = array(':status' => 'Active', ':title' => '%' . trim(Yii::app()->request->getParam('search')) . '%', ':description' => '%' . trim(Yii::app()->request->getParam('search')) . '%');
        $criteria->order = 't.publish_date DESC';
        $blogModel = new CActiveDataProvider('Blog', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 5,
            ),
        ));
        return $blogModel;
    }

    public function afterFind() {
        $description = $this->description;        
        $description = str_replace('\n', '', $description);        
        $description = str_replace('\r', '', $description);   
        $this->description = $description;
    }
}
