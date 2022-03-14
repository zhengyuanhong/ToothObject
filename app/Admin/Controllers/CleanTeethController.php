<?php

namespace App\Admin\Controllers;

use App\Models\CleanTeeth;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CleanTeethController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CleanTeeth';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CleanTeeth());

        $grid->column('id', __('Id'));
        $grid->column('clean_tooth_date', __('Clean tooth date'));
        $grid->column('appoint_content', __('Appoint content'));
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
        $show = new Show(CleanTeeth::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('clean_tooth_date', __('Clean tooth date'));
        $show->field('appoint_content', __('Appoint content'));
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
        $form = new Form(new CleanTeeth());

        $form->text('clean_tooth_date', __('Clean tooth date'));
        $form->textarea('appoint_content', __('Appoint content'));

        return $form;
    }
}
