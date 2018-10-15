<?php
/* @var $this BlogController */
/* @var $model Blog */

$this->breadcrumbs = array(
    'Blog' => array('index'),
    $model->title,
);

$this->menu = array(
    array('label' => 'List Blog', 'url' => array('index')),
    array('label' => 'Create Blog', 'url' => array('create')),
    array('label' => 'Update Blog', 'url' => array('update', 'id' => $model->blog_id)),
    array('label' => 'Delete Blog', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->blog_id), 'confirm' => 'Are you sure you want to delete this item?')),
    array('label' => 'Manage Blog', 'url' => array('admin')),
);
?>

<div id="main-content" class="clearfix main-grid">
    <div id="breadcrumbs">
        <div class="display">
            <ul class="breadcrumb">
                <li class="page-title"><a href="<?php echo Yii::app()->createUrl("admin/blog/admin"); ?>">
                        <?php echo CommonFunctions::getText(LBL_MENU_BLOG_TITLE); ?></a>
                </li>
                <li><i class="<?php echo Yii::app()->params['icon_right']; ?>"></i></li>
                <li><?php echo CommonFunctions::getText(LBL_LIST_VIEW_TITLE); ?></li>
            </ul>
        </div>
        <div class="grid-btns">
            <ul>
                <li>
                    <ul>
                        <li>
                            <?php
                            echo CHtml::htmlButton('<i class="' . Yii::app()->params['icon_back'] . '"></i><span>' . CommonFunctions::getText(LBL_FORM_BACK) . '</span>', array('class' => 'btn btn-cancel', 'onclick' => 'redirectPage("' . Yii::app()->createUrl('admin/blog/admin') . '")'));
                            ?>
                        </li>
                        <li>
                            <?php
                            echo CHtml::htmlButton('<i class="' . Yii::app()->params['icon_remove'] . '"></i><span>' . CommonFunctions::getText(LBL_LIST_DELETE_TITLE) . '</span>', array('class' => 'btn btn-trash', 'onClick' => CHtml::ajax(array(
                                    'type' => 'POST', //request type
                                    'beforeSend' => 'js:function(){return window.confirm("' . CommonFunctions::getText(MSG_RECORD_DELETE_CONFIRM) . '")}',
                                    'url' => Yii::app()->createUrl('admin/blog/ajaxupdate', array('act' => 'doDelete')), //url to call.
                                    'data' => array('autoId[]' => $model->blog_id),
                                    'success' => 'function(result) { redirectPage("' . Yii::app()->createUrl('admin/blog/admin') . '"); }', //alert(result);
                                    'error' => 'function(result){ redirectPage("' . Yii::app()->createUrl('admin/blog/admin') . '"); }' //alert(result);
                            ))));
                            ?>
                        </li>
                        <li>
                            <?php
                            echo CHtml::htmlButton('<i class="' . Yii::app()->params['icon_edit'] . '"></i><span>' . CommonFunctions::getText(LBL_FORM_EDIT) . '</span>', array('class' => 'btn btn-save', 'onclick' => 'redirectPage("' . Yii::app()->createUrl('admin/blog/update', array('id' => $model->blog_id)) . '")'));
                            ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <?php $form_view = $this->beginWidget('CActiveForm'); ?>
    <div class="clearfix" id="page-content">

        <div class="addeditpg bs viewpg">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_BLOG_TITLE); ?></div>
                        <div class="text"><?php echo CHtml::encode(CHtml::value($model, 'title')); ?></div>
                    </div>
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_BLOG_IMAGE); ?></div>
                        <div class="text">
                            <?php if ($model->image <> '' && file_exists($model->blogImgBehavior->getFilePath("thumb"))) {
                                ?>
                                <a href="<?php echo Yii::app()->request->baseUrl . '/' . Yii::app()->params['blogImagePath'] . '/' . $model->image; ?>" title="<?php echo CommonFunctions::replaceQuote(CHtml::value($model, 'title')); ?>" class="<?php echo Yii::app()->params['image_popup_class']; ?>"><?php echo CHtml::image($model->blogImgBehavior->getFileUrl("thumb"), CHtml::value($model, 'title')); ?></a>
                                <br />
                                <?php
                                $file_data = CommonFunctions::extractFileNameDate(CHtml::value($model, 'image_name_date'));
                                echo $file_data[0] . '&nbsp;&nbsp;' . CommonFunctions::formatDateTime($file_data[1], true);
                                ?>
                                <?php
                            } else {
                                echo CHtml::image(Yii::app()->baseUrl."/slir/w100-h100/".Yii::app()->baseUrl."/images/default/No_Blog.jpg",CHtml::value($model,'name'));
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_BLOG_DESCRIPTION); ?></div>
                        <div class="text tinyview_text"><?php echo CHtml::value($model, 'description'); ?></div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_BLOG_AUTHOR); ?></div>
                        <div class="text"><?php echo CHtml::value($model, 'author'); ?></div>
                    </div>
					<div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_BLOG_PROFILE); ?></div>
                        <div class="text"><?php echo CHtml::value($model, 'profile_url'); ?></div>
                    </div>
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_BLOG_PUBLISH_DATE); ?></div>
                        <div class="text"><?php echo CommonFunctions::formatDateTime(CHtml::value($model, 'publish_date'),true); ?></div>
                    </div>
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_BLOG_STATUS); ?></div>
                        <div class="text"><?php echo CHtml::value($model, 'status'); ?></div>
                    </div>
                    <?php
                    if (Yii::app()->params['displaySEOFields']) {
                        ?>
                        <div class="form-group">
                            <div class="title"><?php echo CommonFunctions::getText(LBL_META_TITLE); ?></div>
                            <div class="text"><?php echo CHtml::encode(CHtml::value($model, 'meta_title')); ?></div>
                        </div>
                        <div class="form-group">
                            <div class="title"><?php echo CommonFunctions::getText(LBL_META_KEYWORDS); ?></div>
                            <div class="text"><?php echo CHtml::encode(CHtml::value($model, 'meta_keywords')); ?></div>
                        </div>
                        <div class="form-group">
                            <div class="title"><?php echo CommonFunctions::getText(LBL_META_DESCRIPTION); ?></div>
                            <div class="text"><?php echo CHtml::encode(CHtml::value($model, 'meta_description')); ?></div>
                        </div>
                    <?php } ?> 
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_CREATED_ON); ?></div>
                        <div class="text"><?php echo CommonFunctions::formatDateTime(CHtml::value($model, 'createdon'), true); ?></div>
                    </div>
                    <div class="form-group">
                        <div class="title"><?php echo CommonFunctions::getText(LBL_UPDATED_ON); ?></div>
                        <div class="text"><?php echo CommonFunctions::formatDateTime(CHtml::value($model, 'updatedon'), true); ?></div>
                    </div>


                </div>
            </div>
        </div>

        <?php include_once(Yii::getPathOfAlias('webroot.themes.' . Yii::app()->params['admin_theme'] . '.views.layouts') . '/footer.php'); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
