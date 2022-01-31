<?php

namespace App\Admin\Controllers;

use App\Models\Ad;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AdController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Ad';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ad());

        $grid->column('id', __('Id'));
        $grid->column('note', __('Note'));
        $grid->column('active', __('Active'));
        $grid->column('pv', __('Pv'));
        $grid->column('scope', __('Scope'));
        $grid->column('article_url', __('Article url'));
        $grid->column('image_url', __('Image url'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Ad::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('note', __('Note'));
        $show->field('active', __('Active'));
        $show->field('pv', __('Pv'));
        $show->field('scope', __('Scope'));
        $show->field('article_url', __('Article url'));
        $show->field('image_url', __('Image url'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Ad());

        $form->text('note', __('Note'));
        $form->switch('active', __('Active'))->default(1);
        $form->switch('pv', __('Pv'));
        $form->text('scope', __('Scope'));
        $form->text('article_url', __('Article url'));
        $form->text('image_url', __('Image url'));

        return $form;
    }
}
