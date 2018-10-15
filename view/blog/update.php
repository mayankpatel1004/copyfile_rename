<?php

/* @var $this BlogController */
/* @var $model Blog */

$this->breadcrumbs = array(
    'Blog' => array('index'),
    $model->title => array('view', 'id' => $model->blog_id),
    'Update',
);

$this->menu = array(
    array('label' => 'List Blog', 'url' => array('index')),
    array('label' => 'Create Blog', 'url' => array('create')),
    array('label' => 'View Blog', 'url' => array('view', 'id' => $model->blog_id)),
    array('label' => 'Manage Blog', 'url' => array('admin')),
);
?>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>