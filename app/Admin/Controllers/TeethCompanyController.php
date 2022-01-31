<?php

namespace App\Admin\Controllers;

use App\Models\TeethCompany;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TeethCompanyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'TeethCompany';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TeethCompany());

        $grid->column('id', __('Id'));
        $grid->column('slogan', __('Slogan'));
        $grid->column('card_name', __('Card name'));
        $grid->column('company_name', __('Company name'));
        $grid->column('user_id', __('User id'));
        $grid->column('phone', __('Phone'));
        $grid->column('address', __('Address'));
        $grid->column('lat', __('Lat'));
        $grid->column('lon', __('Lon'));
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
        $show = new Show(TeethCompany::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('slogan', __('Slogan'));
        $show->field('card_name', __('Card name'));
        $show->field('company_name', __('Company name'));
        $show->field('user_id', __('User id'));
        $show->field('phone', __('Phone'));
        $show->field('address', __('Address'));
        $show->field('lat', __('Lat'));
        $show->field('lon', __('Lon'));
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
        $form = new Form(new TeethCompany());

        $form->text('slogan', __('Slogan'));
        $form->text('card_name', __('Card name'));
        $form->text('company_name', __('Company name'));
        $form->switch('user_id', __('User id'));
        $form->mobile('phone', __('Phone'));
        $form->text('address', __('Address'));
        $form->decimal('lat', __('Lat'));
        $form->decimal('lon', __('Lon'));

        return $form;
    }
}
