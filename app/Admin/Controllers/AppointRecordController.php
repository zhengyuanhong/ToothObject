<?php

namespace App\Admin\Controllers;

use App\Models\AppointRecord;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AppointRecordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AppointRecord';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AppointRecord());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('appoint_date', __('Appoint date'));
        $grid->column('type', __('Type'));
        $grid->column('obj_name', __('Obj name'));
        $grid->column('appoint_addr', __('Appoint addr'));
        $grid->column('appoint_date_at', __('Appoint date at'));
        $grid->column('is_cancel', __('Is cancel'));
        $grid->column('appoint_status', __('Appoint status'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('created_at', __('Created at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(AppointRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('appoint_date', __('Appoint date'));
        $show->field('type', __('Type'));
        $show->field('obj_name', __('Obj name'));
        $show->field('appoint_addr', __('Appoint addr'));
        $show->field('appoint_date_at', __('Appoint date at'));
        $show->field('is_cancel', __('Is cancel'));
        $show->field('appoint_status', __('Appoint status'));
        $show->field('updated_at', __('Updated at'));
        $show->field('created_at', __('Created at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AppointRecord());

        $form->number('user_id', __('User id'));
        $form->text('appoint_date', __('Appoint date'));
        $form->text('type', __('Type'));
        $form->text('obj_name', __('Obj name'));
        $form->text('appoint_addr', __('Appoint addr'));
        $form->datetime('appoint_date_at', __('Appoint date at'))->default(date('Y-m-d H:i:s'));
        $form->number('is_cancel', __('Is cancel'));
        $form->switch('appoint_status', __('Appoint status'));

        return $form;
    }
}
