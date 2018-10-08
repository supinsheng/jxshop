<?php
namespace models;

class Brand extends Model
{
    // 设置这个模型对应的表
    protected $table = 'brand';
    // 设置允许接收的字段
    protected $fillable = ['brand_name','logo'];

    public function _before_write(){

        $this->delete_logo();

        $upload = \libs\Uploader::make();
        $path = $upload->upload('logo','brand');

        $path = '/uploads/'.$path;

        $this->data['logo'] = $path;  
    }

    public function _before_delete(){
        
        $this->delete_logo();
    }

    public function delete_logo(){

        if(isset($_GET['id'])){

            $brand = $this->findOne($_GET['id']);

            @unlink(ROOT.'public'.$brand['logo']);
        }
    }
}