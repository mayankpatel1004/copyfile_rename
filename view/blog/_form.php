<?php
/* @var $this BlogController */
/* @var $model Blog */
/* @var $form CActiveForm */

$validation_fields = array('blogcategories_id','title', 'image', 'description', 'author','profile_url');
$focus_field = CommonFunctions::getFocusField($model, $validation_fields);

$form = $this->beginWidget('CActiveForm', array(
    'id' => 'blog-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'), // don't forget this option
    'focus' => array($model, $focus_field),
        ));
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
?>
<div id="main-content" class="clearfix main-grid">
    <div id="breadcrumbs">
        <div class="display">
            <ul class="breadcrumb">
                <li class="page-title"><a tabindex="-1" href="<?php echo Yii::app()->createUrl("admin/blog/admin"); ?>"><?php echo CommonFunctions::getText(LBL_MENU_BLOG_TITLE); ?></a></li>
                <li><i class="<?php echo Yii::app()->params['icon_right']; ?>"></i></li>
                <li><?php echo $model->isNewRecord ? CommonFunctions::getText(LBL_FORM_ADD) : CommonFunctions::getText(LBL_FORM_EDIT); ?></li>
            </ul>
        </div>
        <div class="grid-btns">
            <ul>
                <li>
                    <ul>
                        <li>
                            <?php
                            echo CHtml::htmlButton('<i class="' . Yii::app()->params['icon_back'] . '"></i><span>' . CommonFunctions::getText(LBL_FORM_BACK) . '</span>', array('class' => 'btn btn-cancel', 'tabindex' => 23, 'onclick' => 'redirectPage("' . Yii::app()->createUrl('admin/blog/admin') . '")'));
                            ?>
                        </li>

                        <?php
                        if (!$model->isNewRecord) {
                            echo '<li>' . CHtml::htmlButton('<i class = "' . Yii::app()->params['icon_remove'] . '"></i><span>' . CommonFunctions::getText(LBL_LIST_DELETE_TITLE) . '</span>', array('class' => 'btn btn-trash', 'tabindex' => 22, 'onClick' => CHtml::ajax(array(
                                    'type' => 'POST', //request type
                                    'beforeSend' => 'js:function(){return window.confirm("' . CommonFunctions::getText(MSG_RECORD_DELETE_CONFIRM) . '")}',
                                    'url' => Yii::app()->createUrl('admin/blog/ajaxupdate', array('act' => 'doDelete')), //url to call.
                                    'data' => array('autoId[]' => $model->blog_id),
                                    'success' => 'function(result) { redirectPage("' . Yii::app()->createUrl('admin/blog/admin') . '"); }',
                                    'error' => 'function(result){ redirectPage("' . Yii::app()->createUrl('admin/blog/admin') . '"); }'
                        )))) . '</li>';
                        }
                        ?>

                        <li>
                            <?php
                            echo CHtml::htmlButton('<i class="' . Yii::app()->params['icon_reset'] . '"></i><span>' . CommonFunctions::getText(LBL_FORM_RESET) . '</span>', array('class' => 'btn btn-reset', 'type' => 'reset', 'tabindex' => 21, 'id' => 'btn_reset', 'onclick' => CHtml::ajax(array(
                                    'type' => 'POST', //request type
                                    'url' => $this->createUrl('default/upload'), //url to call.
                                    'data' => array('act' => 'delete', 'fileVar' => Yii::app()->params['blogajaxImageVar'], 'file_name' => "js:function(){ return $('#uploaded_file_name').val();
                            }"),
                                    'success' => 'function(result) { reset_form(); }',
                                    'error' => 'function(result){ alert(result); }'
                            ))));
                            ?>
                        </li>
                        <li>
                            <?php
                            echo CHtml::htmlButton('<i class="' . Yii::app()->params['icon_save'] . '"></i><span>' . CommonFunctions::getText(LBL_FORM_SAVE) . '</span>', array('class' => 'btn btn-save', 'type' => 'submit', 'tabindex' => 20, 'id' => 'btn_submit'));
                            ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="clearfix" id="page-content">
        <div class="addeditpg bs">
            <div class="row">
                <div class="col-md-6">      
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'blogcategories_id', array('class' => 'control-label')); ?>
                        <div class="controls">                                                        
                            <?php echo $form->dropDownList($model, 'blogcategories_id', CHtml::listData(Blogcategories::model()->findAll('status="Active"'), 'blogcategories_id', 'title'), array('class' => 'form-control tt_focus_top', 'tabindex' => 1, 'title' => CommonFunctions::getText(MSG_BLOG_CATEGORY_TIP), 'empty' => 'Select Category')); ?>

                            <?php echo $form->error($model, 'blogcategories_id'); ?>
                        </div>                        
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'title', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model, 'title', array('maxlength' => 100, 'class' => 'form-control tt_focus_bottom', 'tabindex' => 2, 'title' => CommonFunctions::getText(MSG_BLOG_AUTHOR_TIP))); ?>
                            <?php echo $form->error($model, 'title'); ?>
                        </div>
                    </div>
                    <div class="form-group progress_h tt_bottom" title="<?php echo CommonFunctions::getText(MSG_BLOG_IMAGE_TIP); ?>">
                        <?php echo $form->labelEx($model, 'image', array('class' => 'control-label')); ?>
                        <?php
                        if (!Yii::app()->params['ajaxFileUpload']) {
                            echo $form->fileField($model, 'image', array('title' => CommonFunctions::getText(MSG_BLOG_IMAGE_TIP), 'class' => 'tt_bottom', 'tabindex' => 3, 'readonly' => ''));
                        } else {
                            $this->widget('ext.EFineUploader.EFineUploader', array(
                                'id' => 'FineUploader',
                                'config' => array(
                                    'autoUpload' => true,
                                    'tabset' => 3,
                                    'hoverClass' => 'tt_bottom',
                                    'focusClass' => 'tt_bottom',
                                    'request' => array(
                                        'endpoint' => $this->createUrl('default/upload'),
                                        'params' => array('act' => 'upload', 'extVar' => Yii::app()->params['blogImageExt'], 'maxSize' => Yii::app()->params['blogImageMaxSize'], 'fileVar' => Yii::app()->params['blogajaxImageVar'], 'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                                    ),
                                    'retry' => array('enableAuto' => true, 'preventRetryResponseProperty' => true),
                                    'chunking' => array('enable' => true, 'partSize' => Yii::app()->params['FileUploadPartSize']), //bytes
                                    'deleteFile' => array('enabled' => true, 'endpoint' => $this->createUrl('default/upload'), 'forceConfirm' => true, 'params' => array('act' => 'delete', 'fileVar' => Yii::app()->params['blogajaxImageVar'], 'file_name' => "js:function(){ return $('#uploaded_file_name').val(); }")),
                                    'text' => array('deleteButton' => CommonFunctions::getText(LBL_LIST_DELETE_TITLE)),
                                    'classes' => array('success' => 'uploaded_file form-control'),
                                    'callbacks' => array(
                                        'onComplete' => "js:function(id, name, response){ enableFormButtons(); hideBlock('image_display_block'); hideBlock('file_process_div');  $('#uploaded_file_name').val(name); displayBlock('upload_file_list'); hideBlock('uploaded_image_display_block');}",
                                        'onSubmit' => "js:function(id, name){ disableFormButtons(); }",
                                        'onProgress' => "js:function(id, name, loaded, total){ disableFormButtons(); }",
                                        'onCancel' => "js:function(id, name){ enableFormButtons(); }",
                                        'onError' => "js:function(id, name, errorReason){ enableFormButtons(); }",
                                        'onvalidateBatch' => "return false",
                                    ),
                                    'validation' => array(
                                        'allowedExtensions' => explode(",", Yii::app()->params['blogImageExt']), //array('jpg','jpeg')
                                        'sizeLimit' => Yii::app()->params['blogImageMaxSize'], //maximum file size in bytes
                                        'minSizeLimit' => Yii::app()->params['blogImageMinSize'], // minimum file size in bytes
                                    ),
                                )
                            ));
                        }
                        ?>
                        <input type="hidden" name="uploaded_file_name" id="uploaded_file_name" value="" />
                        <?php echo $form->error($model, 'image'); ?>
                        <?php
                        if (Yii::app()->user->hasState(Yii::app()->params['blogajaxImageVar']) && isset($_POST['Blog'])) {
                            $file_info = AdminFunctions::getAjaxFileInfo(Yii::app()->params['blogajaxImageVar']);
                            ?>
                            <div class="uploaded_file col-md-12" id="uploaded_image_display_block">
                                <a class="name <?php echo Yii::app()->params['image_popup_class']; ?>" href="<?php echo $file_info[1]; ?>" title="<?php echo CommonFunctions::replaceQuote(CHtml::value($model, 'title')); ?>"><?php echo $file_info[0]; ?></a>&nbsp;&nbsp;<?php echo $file_info[2]; ?>
                                <?php
                                echo CHtml::ajaxLink(CommonFunctions::getText(LBL_LIST_DELETE_TITLE), array('default/upload', 'act' => 'delete', 'dataType' => 'json', 'fileVar' => Yii::app()->params['blogajaxImageVar'], 'file_name' => $file_info[0]), array('type' => 'POST', 'success' => 'js:function(data){hideBlock("uploaded_image_display_block");}'), array('class' => 'delete'));
                                ?>
                            </div>

                            <?php
                        } elseif ($model->image <> '' && file_exists($model->blogImgBehavior->getFilePath())) {
                            $file_data = CommonFunctions::extractFileNameDate(CHtml::value($model, 'image_name_date'));
                            if (is_array($file_data)) {
                                ?>
                                <div class="uploaded_file col-md-12" id="image_display_block">
                                    <a class="name <?php echo Yii::app()->params['image_popup_class']; ?>" href="<?php echo Yii::app()->request->baseUrl . '/' . Yii::app()->params['blogImagePath'] . '/' . $model->image; ?>" title="<?php echo CommonFunctions::replaceQuote(CHtml::value($model, 'title')); ?>"><?php echo $file_data[0]; ?></a>&nbsp;&nbsp;<?php echo CommonFunctions::formatDateTime($file_data[1], true); ?>

                                    <?php echo CHtml::link(CommonFunctions::getText(LBL_LIST_DELETE_TITLE), Yii::app()->createUrl('admin/blog/deleteimg', array('id' => $model->blog_id)), array('class' => 'delete', 'title' => CommonFunctions::getText(LBL_DELETE_BLOG_IMAGE), 'onclick' => 'return window.confirm("' . CommonFunctions::getText(MSG_IMAGE_DELETE_CONFIRM) . '"); ')); ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'description', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?php
                            $this->widget('ext.tinymce.TinyMce', array(
                                'model' => $model,
                                'attribute' => 'description',
                                'htmlOptions' => array(
                                    'rows' => 20,
                                    'tabindex' => 4,
                                ),
                            ));
                            ?>
                            <?php echo $form->error($model, 'description'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'author', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?php
                            if (Yii::app()->params['blog_user_auto'])
                                echo $form->textField($model, 'author', array('maxlength' => 100, 'class' => 'form-control tt_focus_bottom', 'tabindex' => 5, 'value' => Yii::app()->user->getState('full_name'), 'readonly' => 'readonly'));
                            else
                                echo $form->textField($model, 'author', array('maxlength' => 100, 'class' => 'form-control tt_focus_bottom', 'tabindex' => 5, 'title' => CommonFunctions::getText(MSG_BLOG_TITLE_TIP)));
                            ?>
                            <?php echo $form->error($model, 'author'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'profile_url', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model, 'profile_url', array('class' => 'form-control tt_focus_bottom', 'tabindex' => 6, 'title' => CommonFunctions::getText(MSG_BLOG_PROFILE_TIP))); ?>
                            <?php echo $form->error($model, 'profile_url'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'publish_date', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?php
                            if (Yii::app()->params['TIME_FIELD_TYPE'] == 'Yes')
                                $controlType = '';
                            else
                                $controlType = 'select';

                            $this->widget('CJuiDateTimePicker', array(
                                'model' => $model, //Model object
                                'value' => CommonFunctions::formatDateTime(CHtml::value($model, 'publish_date'), true),
                                'name' => 'Blog[publish_date]',
                                'id' => 'Blog_publish_date',
                                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                                'options' => array(
                                    'dateFormat' => 'mm/dd/yy',
                                    'timeFormat' => 'HH:mm',
                                    'controlType' => $controlType,
                                    'showAnim' => 'fold',
                                    'changeYear' => true,
                                    'changeMonth' => true,
                                    'timezone' => 'CT',
                                    'buttonText' => 'Select Publish Date',
                                ),
                                'htmlOptions' => array(
                                    'maxlength' => 10,
                                    'readonly' => 'readonly',
                                    'tabindex' => 7,
                                    'class' => 'form-control',
                                //'title'=>CommonFunctions::getText(MSG_BLOG_DATE_TIP),
                                ),
                            ));
                            ?>
                            <?php echo $form->error($model, 'publish_date'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'status', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->dropDownList($model, 'status', array('Active' => CommonFunctions::getText(LBL_FORM_STATUS_ACTIVE), 'Inactive' => CommonFunctions::getText(LBL_FORM_STATUS_INACTIVE)), array('title' => CommonFunctions::getText(MSG_STATUS_TIP), 'tabindex' => 8, 'class' => 'form-control tt_focus_top')); ?>
                            <?php echo $form->error($model, 'status'); ?>
                        </div>
                    </div>

                    <?php
                    if (Yii::app()->params['displaySEOFields']) {
                        if (trim($model->meta_title) != '' || trim($model->meta_keywords) != '' || trim($model->meta_description) != '') {
                            $show_add_more = false;
                            $show_hide_more = true;
                        } else {
                            $show_add_more = true;
                            $show_hide_more = false;
                        }
                        echo CommonFunctions::displayMoreFieldsBlock($show_add_more, $show_hide_more);
                        ?>
                        <div id="add-more-info" <?php if (Yii::app()->params['INC_MORE_FIELDS_OPTION'] && $show_add_more) echo 'style = "display:none;"'; ?>>
                            <div class="form-group">
                                <?php echo $form->labelEx($model, 'meta_title', array('class' => 'control-label')); ?>
                                <div class="controls">
                                    <?php echo $form->textField($model, 'meta_title', array('class' => 'form-control tt_focus_bottom', 'tabindex' => 9, 'title' => CommonFunctions::getText(MSG_META_TITLE_TIP))); ?>
                                    <?php echo $form->error($model, 'meta_title'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model, 'meta_keywords', array('class' => 'control-label')); ?>
                                <div class="controls">
                                    <?php echo $form->textArea($model, 'meta_keywords', array('class' => 'form-control tt_focus_bottom', 'tabindex' => 10, 'title' => CommonFunctions::getText(MSG_META_KEYWORDS_TIP))); ?>
                                    <?php echo $form->error($model, 'meta_keywords'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model, 'meta_description', array('class' => 'control-label')); ?>
                                <div class="controls">
                                    <?php echo $form->textArea($model, 'meta_description', array('class' => 'form-control tt_focus_bottom', 'title' => CommonFunctions::getText(MSG_META_DESCRIPTION_TIP), 'tabindex' => 11)); ?>
                                    <?php echo $form->error($model, 'meta_description'); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?> 


                </div>
            </div>

        </div>

        <?php include_once(Yii::getPathOfAlias('webroot.themes.' . Yii::app()->params['admin_theme'] . '.views.layouts') . '/footer.php'); ?>
    </div>
</div>
<?php $this->endWidget(); ?>    
<script type="text/javascript">
    $('#blog-form').submit(function () {
        $("#btn_submit").focus();
        $('#btn_submit').attr("disabled", true);
    });
    /* $('#Blog_image').ace_file_input({
        no_file: 'No File ...',
        btn_choose: 'Choose',
        btn_change: 'Change',
        droppable: false,
        onchange: null,
        thumbnail: false, //| true | large
        whitelist: 'png|gif|jpg|jpeg'
    }); */
    function reset_form() {
        hideBlock("upload_file_list");
<?php
if ($model->image <> '' && file_exists($model->blogImgBehavior->getFilePath("thumb"))) {
    ?>
            displayBlock("image_display_block");
<?php } ?>
    }
<?php if ($focus_field == 'description') { ?>
        focusEditor("Blog_description");
<?php } ?>
</script>