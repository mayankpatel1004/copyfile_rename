<?php

/* @var $this BlogController */
/* @var $model Blog */

$this->breadcrumbs = array(
    'Blog' => array('index'),
    'Create',
);

$this->menu = array(
    array('label' => 'List Blog', 'url' => array('index')),
    array('label' => 'Manage Blog', 'url' => array('admin')),
);
?>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>