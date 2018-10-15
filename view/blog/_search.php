<?php
/* @var $this BlogController */
/* @var $model Blog */
/* @var $form CActiveForm */
?>

<div class="search-grid">
    <div class="search-header">
        <h4><i class="<?php echo Yii::app()->params['icon_search']; ?>"></i><?php echo CommonFunctions::getText(LBL_LIST_SEARCH_TITLE); ?></h4>
        <a href="Javascript: void(0);" class="search-close"><i class="<?php echo Yii::app()->params['icon_close']; ?>"></i></a> </div>
    <div class="row search-box bs">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
            'id' => 'search_form',
            'htmlOptions' => array('class' => 'form-vertical'),
        ));
        ?>
        
            <div class="col-md-4 form-group">
                <?php //echo $form->label($model,'name',array('class'=>'control-label'));  ?>
                <label class="control-label"><?php echo CommonFunctions::getText(LBL_BLOG_TITLE) . CommonFunctions::getText(LBL_SEARCH_FIELD_SEPERATOR) . CommonFunctions::getText(LBL_BLOG_DESCRIPTION); ?></label>
                <div class="controls">
                    <?php
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'model' => $model,
                        'attribute' => 'title',
                        'scriptFile' => false,
                        'cssFile' => false,
                        'source' => $this->createUrl('default/autoCompleteList', array('table' => $model->tableName(), 'select' => 'title', 'match' => array('title', 'description'))),
                        'htmlOptions' => array(
                            'class' => 'form-control',
                        ),
                        // additional javascript options for the autocomplete plugin
                        'options' => array(
                            'minLength' => Yii::app()->params['autoCompleteMinLength'],
                        ),
                    ));
                    ?>
                </div>
            </div>
            <div class="col-md-4 form-group"> <?php echo $form->label($model, 'status', array('class' => 'control-label')); ?>
                <div class="controls"> <?php echo $form->dropDownList($model, 'status', array('' => CommonFunctions::getText(LBL_BLOG_STATUS_PROMPT), 'Active' => CommonFunctions::getText(LBL_FORM_STATUS_ACTIVE), 'Inactive' => CommonFunctions::getText(LBL_FORM_STATUS_INACTIVE)), array('class' => 'form-control')); ?> </div>
            </div>
        
        <div class="col-md-12">
            <?php
            echo CHtml::submitButton(CommonFunctions::getText(LBL_FORM_SEARCH), array('class' => 'btn btn-default'));
            echo CHtml::submitButton(CommonFunctions::getText(LBL_FORM_RESET), array('class' => 'btn btn-default ml5', 'onClick' => 'js:clearForm("#search_form");'));
            ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<!-- search-form -->