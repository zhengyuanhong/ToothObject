<?php

namespace App\Admin\Controllers;

use App\Models\WechatUser;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WechatUserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'WechatUser';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WechatUser());

        $grid->column('id', __('Id'));
        $grid->column('role', __('Role'));
        $grid->column('avatar', __('Avatar'));
        $grid->column('name', __('Name'));
        $grid->column('gender', __('Gender'));
        $grid->column('openid', __('Openid'));
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
        $show = new Show(WechatUser::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('role', __('Role'));
        $show->field('avatar', __('Avatar'));
        $show->field('name', __('Name'));
        $show->field('gender', __('Gender'));
        $show->field('token', __('Token'));
        $show->field('openid', __('Openid'));
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
        $form = new Form(new WechatUser());

        $form->text('role', __('Role'));
        $form->image('avatar', __('Avatar'));
        $form->text('name', __('Name'));
        $form->switch('gender', __('Gender'));
        $form->text('token', __('Token'));
        $form->text('openid', __('Openid'));

        return $form;
    }
}
