<?php
namespace controllers;

use models\Article;

class ArticleController extends BaseController
{
    // 列表页
    public function index()
    {
        $model = new Article;
        $data = $model->findAll();
        view('article/index', $data);
    }

    // 显示添加的表单
    public function create()
    {
        $model = new \models\Article_category;
        $data = $model->findAll();
        view('article/create',['data'=>$data['data']]);
    }

    // 处理添加表单
    public function insert()
    {
        $model = new Article;
        $model->fill($_POST);
        $model->insert();
        redirect('/article/index');
    }

    // 显示修改的表单
    public function edit()
    {
        $model = new Article;
        $data=$model->findOne($_GET['id']);
        view('article/edit', [
            'data' => $data,    
        ]);
    }

    // 修改表单的方法
    public function update()
    {
        $model = new Article;
        $model->fill($_POST);
        $model->update($_GET['id']);
        redirect('/article/index');
    }

    // 删除
    public function delete()
    {
        $model = new Article;
        $model->delete($_GET['id']);
        redirect('/article/index');
    }
}