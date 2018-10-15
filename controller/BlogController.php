<?php

class BlogController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout='//layouts/column2';
    public function init() {
        if (!Yii::app()->getModule('admin')->user->getId()) {
            $this->redirect(Yii::app()->getModule('admin')->user->loginUrl);
        }
        AdminLoginForm::checkUserStatus();
    }

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow admin user to perform 'admin index view create update' and 'delete' actions
                'actions' => array('index', 'view', 'create', 'update', 'admin', 'delete', 'toggle', 'switch', 'ajaxupdate', 'deleteimg'),
                'expression' => 'Yii::app()->getModule("admin")->user->getId()',
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /* Change sort order and status */

    public function actions() {
        return array(
            'toggle' => 'ext.jtogglecolumn.ToggleAction',
            'switch' => 'ext.jtogglecolumn.SwitchAction', // only if you need it
        );
    }

    /* actions for multiple active/inactive/delete */

    public function actionAjaxupdate() {
        $act = $_GET['act'];
        //echo $act;print_r($_POST);die();
        $autoIdAll = $_POST['autoId'];
        $error = 0;
        if (count($autoIdAll) > 0) {
            foreach ($autoIdAll as $autoId) {
                $model = $this->loadModel($autoId);
                if ($act == 'doDelete') {
                    //Delete Comments                    
                    BlogComments::model()->deleteAll('blog_id=:blog_id', array(':blog_id' => $autoId));
                    //Delete Blog & Image
                    $mod_delete = $this->loadModel($autoId);
                    $imagefile = $mod_delete->image;
                    if ($imagefile <> '') {
                        CommonFunctions::deleteFile(Yii::app()->params['blogImagePath'], $imagefile, true);
                    }
                    $this->loadModel($autoId)->delete();
                    //$mode = "delete";
                    $message = CommonFunctions::getText(MSG_RECORD_DELETE_SUCCESS);
                    $error = 0;  //if($error == 0) 
                }

                if ($act == 'doActive') {
                    $new_status = 'Active';
                    AdminFunctions::updateStatusField($model, 'status', $new_status, $autoId);
                    $message = CommonFunctions::getText(MSG_RECORD_ACTIVATE_SUCCESS);
                    $error = 0;
                    //$this->renderPartial('EUserFlash');
                }
                if ($act == 'doInactive') {
                    $new_status = 'Inactive';
                    AdminFunctions::updateStatusField($model, 'status', $new_status, $autoId);
                    $message = CommonFunctions::getText(MSG_RECORD_DEACTIVATE_SUCCESS);
                    $error = 0;
                }
            }
            if ($error == 0)
                EUserFlash::setSuccessMessage($message);
            else
                EUserFlash::setErrorMessage($message);
            echo $message . '@@@@' . $error;
        }
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Blog;
        if (!Yii::app()->params['blog_user_auto'])
            $model->scenario = 'bloguser';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $model->publish_date = date("m/d/Y H:i:s");
        if (isset($_POST['Blog'])) {
            $model->attributes = $_POST['Blog'];
            $model->createdon = new CDbExpression('NOW()');
            $model->page_address_identifier = CommonFunctions::getUrlFriendlyString($model->title, $model->tableName(), 0, 'blog_id');
            $model->publish_date = (trim($_POST['Blog']['publish_date']) != '' ? date(Yii::app()->params['mysqlDateTimeFormat'], strtotime($_POST['Blog']['publish_date'])) : '');
            //Start a transaction in case something goes wrong
            if (Yii::app()->params['ajaxFileUpload'])
                $model->image = Yii::app()->user->getState(Yii::app()->params['blogajaxImageVar']);
            $transaction = $model->dbConnection->beginTransaction();
            try {
                //Save the model to the database
                if ($model->save()) {
                    /* $fileurl = $model->blogImgBehavior->FileUrl;
                      $filename = substr($fileurl,strrpos($fileurl, "/")+1);
                      CommonFunctions::updateFileField($filename,$model->blog_id,'blog_id','image',$model->tableName()); */
                    $file_name = CUploadedFile::getInstance($model, 'image');
                    if ($file_name !== null) {
                        $fileurl = $model->blogImgBehavior->FileUrl;
                        $filemask = substr($fileurl, strrpos($fileurl, "/") + 1);
                        CommonFunctions::updateFileField($filemask, $model->blog_id, 'blog_id', 'image', $model->tableName(), 'image_name_date', $file_name);
                    }
                    //update page identifier
                    if (substr($model->page_address_identifier, -2) == '-0') {
                        CommonFunctions::updatePageAddressField($model->tableName(), "page_address_identifier", substr($model->page_address_identifier, 0, strlen($model->page_address_identifier) - 1) . $model->blog_id, 'blog_id', $model->blog_id);
                    }

                    $transaction->commit();
                    EUserFlash::setSuccessMessage(CommonFunctions::getText(MSG_RECORD_INSERT_SUCCESS));
                    $this->redirect(array('admin'));
                }
            } catch (Exception $e) {
                $transaction->rollback();
                Yii::app()->handleException($e);
                $message = $e->getMessage();
                EUserFlash::setErrorMessage($message);
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        if (!Yii::app()->params['blog_user_auto'])
            $model->scenario = 'bloguser';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Blog'])) {

            $model->attributes = $_POST['Blog'];
            $model->publish_date = (trim($_POST['Blog']['publish_date']) != '' ? date(Yii::app()->params['mysqlDateTimeFormat'], strtotime($_POST['Blog']['publish_date'])) : '');
            $model->updatedon = new CDbExpression('NOW()');
            $model->page_address_identifier = CommonFunctions::getUrlFriendlyString($model->title, $model->tableName(), $id, 'blog_id');
            //Start a transaction in case something goes wrong
            $transaction = $model->dbConnection->beginTransaction();
            try {
                //Save the model to the database
                if ($model->save()) {
                    /* $fileurl = $model->blogImgBehavior->FileUrl;
                      $filename = substr($fileurl,strrpos($fileurl, "/")+1);
                      if($filename=='') {
                      $filename=$_POST['old_image'];
                      }
                      CommonFunctions::updateFileField($filename,$id,'blog_id','image',$model->tableName()); */
                    $file_name = CUploadedFile::getInstance($model, 'image');
                    if ($file_name !== null) {
                        $fileurl = $model->blogImgBehavior->FileUrl;
                        $filemask = substr($fileurl, strrpos($fileurl, "/") + 1);
                        CommonFunctions::updateFileField($filemask, $id, 'blog_id', 'image', $model->tableName(), 'image_name_date', $file_name);
                    }

                    $transaction->commit();
                    EUserFlash::setSuccessMessage(CommonFunctions::getText(MSG_RECORD_UPDATE_SUCCESS));
                    $this->redirect(array('admin'));
                }
            } catch (Exception $e) {
                $transaction->rollback();
                Yii::app()->handleException($e);
                $message = $e->getMessage();
                EUserFlash::setErrorMessage($message);
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            $model = $this->loadModel($id);
            $imagefile = $model->image;
            if ($imagefile <> '') {
                CommonFunctions::deleteFile(Yii::app()->params['blogImagePath'], $imagefile, true);
            }
            $model->delete();
            echo "<div class='flash-success'>" . CommonFunctions::getText(MSG_RECORD_DELETE_SUCCESS) . "</div>";

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, CommonFunctions::getText(MSG_ERROR_400_INVALID_REQUEST));
    }

    /**
     * Lists all models.

      public function actionIndex()
      {
      $dataProvider=new CActiveDataProvider('Blog');
      $this->render('index',array(
      'dataProvider'=>$dataProvider,
      ));
      }
     */

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        // unset uploaded file
        Yii::app()->user->setState(Yii::app()->params['blogajaxImageVar'], null);

        $model = new Blog('search');
        // This is as suggested on the Remember-filter-gridview, 
        // to clear the state of the filter. 
        // I add four line to clear the page and sort as well.
        // Note: somehow, I need to put those lines above the 
        // EButtonColumnWithClearFilters, otherwise, it will not clear the 
        // Artist_page and Artist_sort states.
        if (intval(Yii::app()->request->getParam('clearFilters')) == 1) {
            Yii::app()->user->setState('Blog_page', null);
            unset($_GET['Blog_page']);
            Yii::app()->user->setState('Blog_sort', null);
            unset($_GET['sort']);
            EButtonColumnWithClearFilters::clearFilters($this, $model); //where $this is the controller
        }
        // This portion of code is belongs to Page size dropdown.
        $pageSize = Yii::app()->user->getState('blogPageSize', Yii::app()->params['RECORDPERPAGE_ADMIN']);
        if (isset($_GET['pageSize'])) {
            $pageSize = (int) $_GET['pageSize'];
            Yii::app()->user->setState('blogPageSize', $pageSize);
            unset($_GET['pageSize']);
        }
        // I achieve similar result to Page size with the Artist_page.
        // ELSE body is needed to handle the differences of case back to first page from
        // other pages and call to admin page from anywhere. The ajax method is the key to
        // differentiate it. Call to admin would not be an ajax call, while the call from 
        // pagination is an ajax call (by default. since you can make it non ajax as well).
        if (isset($_GET['Blog_page'])) {
            $blogPage = (int) $_GET['Blog_page'] - 1;
            Yii::app()->user->setState('Blog_page', $blogPage);
            unset($_GET['Blog_page']);
        } else if (isset($_GET['ajax'])) {
            Yii::app()->user->setState('Blog_page', 0);
        }
        // Sort is little bit different. Do not UNSET the sort variable 
        // since it will be directly used by the CGridView.
        // The ELSE body is used to handle non ajax calls to admin page.
        if (isset($_GET['sort'])) {
            $blogSort = $_GET['sort'];
            Yii::app()->user->setState('Blog_sort', $blogSort);
        } else if (Yii::app()->user->hasState('Blog_sort')) {
            $_GET['sort'] = Yii::app()->user->getState('Blog_sort');
        }

        $this->render('admin', array(
            'model' => $model,
            'pageSize' => $pageSize,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Blog the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Blog::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Blog $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'blog-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /* Delete blog image with db field update */

    public function actionDeleteimg($id) {
        $model = $this->loadModel($id);
        $imagefile = $model->image;
        if ($imagefile <> '') {
            CommonFunctions::deleteFile(Yii::app()->params['blogImagePath'], $imagefile, true);
            CommonFunctions::updateFileField('', $id, 'blog_id', 'image', $model->tableName(), 'image_name_date');
        }
        EUserFlash::setSuccessMessage(CommonFunctions::getText(MSG_IMAGE_DELETE_SUCCESS));
        $this->redirect(array('update', 'id' => $model->blog_id));
    }

}
