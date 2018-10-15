<?php
/* @var $this BlogController */
/* @var $model Blog */

$dataProvider = $model->search();

$this->breadcrumbs = array(
    'Blog' => array('index'),
    'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.btn-search').click(function(){
	$('.search_h').show();
	return false;
});
$('.search_h form').submit(function(){
	$.fn.yiiGridView.update('blog_grid', {
		data: $(this).serialize()
	});
	return false;
});
$('.search-close').click(function(e) {
	clearForm('#search_form');
	$('#search_form').submit();
	$.fn.yiiGridView.update();
	$('.search_h').hide();
	return false;
});
");
?>

<div id="main-content" class="clearfix main-grid">
    <div id="breadcrumbs">
        <div class="display">
            <ul class="breadcrumb">
                <li class="page-title"><?php echo CommonFunctions::getText(LBL_MENU_BLOG_TITLE); ?></li>
            </ul>
        </div>
        <div class="grid-btns">
            <ul>
                <li>
                    <ul id="action_buttons" style="display:none;">
                        <li>
                            <?php
                            echo CHtml::ajaxLink("<i class='" . Yii::app()->params['icon_active'] . "'></i><span>" . CommonFunctions::getText(LBL_STATUS_ACTIVE) . "</span>", array('blog/ajaxupdate', 'act' => 'doActive', 'dataType' => 'json'), array('type' => 'POST', 'data' => 'js:$("#blog_form").serialize()', 'success' => 'js:function(data){reloadGrid(data,"blog_grid");}', 'beforeSend' => 'js:function(){return validateGridChecked("blog_grid","' . CommonFunctions::getText(MSG_NONE_RECORD_SELECT_ERROR) . '");}'), array('class' => 'btn btn-active'));
                            ?>
                        </li>
                        <li>
                            <?php
                            echo CHtml::ajaxLink("<i class='" . Yii::app()->params['icon_inactive'] . "'></i><span>" . CommonFunctions::getText(LBL_STATUS_INACTIVE) . "</span>", array('blog/ajaxupdate', 'act' => 'doInactive', 'dataType' => 'json'), array('type' => 'POST', 'data' => 'js:$("#blog_form").serialize()', 'success' => 'js:function(data){reloadGrid(data,"blog_grid");}', 'beforeSend' => 'js:function(){return validateGridChecked("blog_grid","' . CommonFunctions::getText(MSG_NONE_RECORD_SELECT_ERROR) . '");}'), array('class' => 'btn btn-inactive'));
                            ?>
                        </li>
                        <li>
                            <?php
                            echo CHtml::ajaxLink("<i class='" . Yii::app()->params['icon_remove'] . "'></i><span>" . CommonFunctions::getText(LBL_LIST_DELETE_TITLE) . "</span>", array('blog/ajaxupdate', 'act' => 'doDelete', 'dataType' => 'json'), array('type' => 'POST', 'data' => 'js:$("#blog_form").serialize()', 'success' => 'js:function(data){reloadGrid(data,"blog_grid");}', 'beforeSend' => 'js:function(){return validateGridDelete("blog_grid","' . CommonFunctions::getText(MSG_NONE_RECORD_SELECT_ERROR) . '","' . CommonFunctions::getText(MSG_RECORD_DELETE_CONFIRM) . '");}'), array('class' => 'btn btn-trash'));
                            ?>
                        </li>
                    </ul>
                </li>
                <li>
                    <ul id="search_add_buttons">
                        <?php if ($dataProvider->totalItemCount > 0) { ?>
                            <li><a href="Javascript: void(0);" class="btn btn-search"><i class="<?php echo Yii::app()->params['icon_search']; ?>"></i><span><?php echo CommonFunctions::getText(LBL_LIST_SEARCH_TITLE); ?></span></a></li>
                        <?php } ?>
                        <li><a href="<?php echo Yii::app()->createUrl('admin/blog/create'); ?>" class="btn btn-addnew"><i class="<?php echo Yii::app()->params['icon_add']; ?>"></i><span><?php echo CommonFunctions::getText(LBL_LIST_ADD_TITLE); ?></span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="clearfix" id="page-content">
        <?php
        if (isset($model->title) || isset($model->description) || isset($model->status))
            $from_display = 'block';
        else
            $from_display = 'none';
        ?>
        <div class="row-fluid search_h" style="display:<?php echo $from_display; ?>">
            <?php
            $this->renderPartial('_search', array(
                'model' => $model,
            ));
            ?>
        </div>
        <?php
        $form_grid = $this->beginWidget('CActiveForm', array(
            'enableAjaxValidation' => true,
            'id' => 'blog_form',
            'htmlOptions' => array('class' => 'grid'),
        ));
        ?>
        <?php
        $extraColumns = array('publish_date', 'Comments');
        $extraColExp = array('CommonFunctions::formatDateTime($data->publish_date,true)', 'CHtml::link("View (".BlogComments::model()->count("blog_id=$data->blog_id AND status<>\'Deleted\'").")",Yii::app()->createUrl("admin/blogComments/admin",array("id"=>$data->blog_id)))');
        $this->widget('ext.selgridview.SelGridView', array(
            'id' => 'blog_grid',
            'dataProvider' => $dataProvider,
            'pageSizeVar' => 'blog',
            'columns' => array(
                array('class' => 'JToggleColumn',
                    'name' => 'status', // boolean model attribute (tinyint(1) with values 0 or 1)
                    'action' => 'switch', // other action, default is 'toggle' action
                    'checkedButtonLabel' => CommonFunctions::getText(LBL_LIST_MAKE_INACTIVE_TITLE), // tooltip
                    'uncheckedButtonLabel' => CommonFunctions::getText(LBL_LIST_MAKE_ACTIVE_TITLE), // tooltip
                    'activeClass' => 'active',
                    'inactiveClass' => 'inactive',
                    'modelClass' => 'Blog',
                ),
                array(
                    'id' => 'autoId',
                    'class' => 'gridCheckBoxColumn',
                    'headerHtmlOptions' => array('class' => 'checkbox'),
                    'htmlOptions' => array('class' => 'checkbox'),
                ),
                array(
                    'class' => 'gridDataColumn',
                    'name' => 'title',
                    'maincell' => 1,
                    'extraColumns' => $extraColumns,
                    'extraColExp' => $extraColExp,
                    'htmlOptions' => array('class' => 'maincell'),
                    'headerHtmlOptions' => array('title' => CommonFunctions::getText(LBL_BLOG_LIST_SORT_TITLE))
                ),
                array(
                    'header' => 'Category',
                    'class' => 'gridDataColumn',
                    'name' => 'bcat.title',
                    'headerHtmlOptions' => array('title' => CommonFunctions::getText(LBL_LIST_SORT_CATEGORY_TITLE))
                ),
                array(
                    'header' => 'Comments',
                    'type' => 'html',
                    'class' => 'gridDataColumn',
                    'value' => 'CHtml::link("View (".BlogComments::model()->count("blog_id=$data->blog_id").")",Yii::app()->createUrl("admin/blogComments/admin",array("id"=>$data->blog_id)), array("title"=>"View Comments list"))',
                    'headerHtmlOptions' => array('class' => 'w100')
                ),
                array(
                    'class' => 'gridDataColumn',
                    'name' => 'publish_date',
                    'value' => 'CommonFunctions::formatDateTime($data->publish_date,true)',
                    'headerHtmlOptions' => array('class' => 'w100', 'title' => CommonFunctions::getText(LBL_LIST_SORT_DATE_TITLE))
                )
            ),
        ));
        ?>
        <?php $this->endWidget(); ?>
        <?php include_once(Yii::getPathOfAlias('webroot.themes.' . Yii::app()->params['admin_theme'] . '.views.layouts') . '/footer.php'); ?>
    </div>
</div>
