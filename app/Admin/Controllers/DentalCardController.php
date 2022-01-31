<?php

namespace App\Admin\Controllers;

use App\Models\DentalCard;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DentalCardController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'DentalCard';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DentalCard());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('name', __('Name'));
        $grid->column('number', __('Number'));
        $grid->column('phone', __('Phone'));
        $grid->column('check_number', __('Check number'));
        $grid->column('expired_at', __('Expired at'));
        $grid->column('integral', __('Integral'));
        $grid->column('is_receive', __('Is receive'));
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
        $show = new Show(DentalCard::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('name', __('Name'));
        $show->field('number', __('Number'));
        $show->field('phone', __('Phone'));
        $show->field('check_number', __('Check number'));
        $show->field('expired_at', __('Expired at'));
        $show->field('integral', __('Integral'));
        $show->field('is_receive', __('Is receive'));
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
        $form = new Form(new DentalCard());

        $form->number('user_id', __('User id'));
        $form->text('name', __('Name'));
        $form->text('number', __('Number'));
        $form->mobile('phone', __('Phone'));
        $form->switch('check_number', __('Check number'));
        $form->date('expired_at', __('Expired at'))->default(date('Y-m-d'));
        $form->switch('integral', __('Integral'));
        $form->switch('is_receive', __('Is receive'));

        return $form;
    }
}
