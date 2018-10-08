<?php
namespace models;

class Goods extends Model
{
    // 设置这个模型对应的表
    protected $table = 'goods';
    // 设置允许接收的字段
    protected $fillable = ['goods_name','logo','is_on_sale','description','cat1_id','cat2_id','cat3_id','brand_id'];

    public function _before_write(){

        if(isset($_GET['id'])){

            $good = $this->findOne($_GET['id']);
            @unlink(ROOT.'public'.$good['logo']);
        }

        $upload = \libs\Uploader::make();
        $path = $upload->upload('logo','goods');
        $path = '/uploads/'.$path;
        
        $this->data['logo'] = $path;
    }

    public function _after_write(){

        $stmt = $this->_db->prepare("INSERT INTO goods_attribute(attr_name,attr_value,goods_id) VALUES(?,?,?)");

        foreach($_POST['attr_name'] as $k=>$v){

            $stmt->execute([
                $v,
                $_POST['attr_value'][$k],
                $this->data['id']
            ]);
        }

        $stmt = $this->_db->prepare("INSERT INTO goods_sku(goods_id,sku_name,stock,price) VALUES(?,?,?,?)");

        foreach($_POST['sku_name'] as $k=>$v){

            $stmt->execute([
                $this->data['id'],
                $v,
                $_POST['stock'][$k],
                $_POST['price'][$k]
            ]);
        }

        $stmt = $this->_db->prepare("INSERT INTO goods_image(goods_id,path) VALUES(?,?)");

        $upload = \libs\Uploader::make();

        $tmp = [];

        foreach($_FILES['image']['name'] as $k=>$v){

            $tmp['name'] = $v;
            $tmp['type'] = $_FILES['image']['type'][$k];
            $tmp['tmp_name'] = $_FILES['image']['tmp_name'][$k];
            $tmp['error'] = $_FILES['image']['error'][$k];
            $tmp['size'] = $_FILES['image']['size'][$k];

            $_FILES['tmp'] = $tmp;

            $path = $upload->upload('tmp','goods');
            $path = '/uploads/'.$path;

            $stmt->execute([
                $this->data['id'],
                $path
            ]);
        }
    }

}