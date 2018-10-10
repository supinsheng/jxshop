<?php
namespace models;

class Goods extends Model
{
    // 设置这个模型对应的表
    protected $table = 'goods';
    // 设置允许接收的字段
    protected $fillable = ['goods_name','logo','is_on_sale','description','cat1_id','cat2_id','cat3_id','brand_id'];

    public function _before_write(){

        if($_FILES['logo']['error'] == 0){

            if(isset($_GET['id'])){

                $good = $this->findOne($_GET['id']);
                @unlink(ROOT.'public'.$good['logo']);
            }
    
            $upload = \libs\Uploader::make();
            $path = $upload->upload('logo','goods');
            $path = '/uploads/'.$path;
            
            $this->data['logo'] = $path;
        }
    }

    public function _after_write(){

        $goodsId = isset($_GET['id']) ? $_GET['id'] : $this->data['id'];

        // 先删除原来的属性
        $stmt = $this->_db->prepare("DELETE FROM goods_attribute WHERE goods_id=?");
        $stmt->execute([$goodsId]);

        $stmt = $this->_db->prepare("INSERT INTO goods_attribute(attr_name,attr_value,goods_id) VALUES(?,?,?)");

        foreach($_POST['attr_name'] as $k=>$v){

            $stmt->execute([
                $v,
                $_POST['attr_value'][$k],
                $goodsId
            ]);
        }

        // 先删除原来的SKU
        $stmt = $this->_db->prepare("DELETE FROM goods_sku WHERE goods_id=?");
        $stmt->execute([$goodsId]);

        $stmt = $this->_db->prepare("INSERT INTO goods_sku(goods_id,sku_name,stock,price) VALUES(?,?,?,?)");

        foreach($_POST['sku_name'] as $k=>$v){

            $stmt->execute([
                $goodsId,
                $v,
                $_POST['stock'][$k],
                $_POST['price'][$k]
            ]);
        }

        // 如果有要删除的图片，那就删除
        if(isset($_POST['del_image']) && $_POST['del_image'] != ''){
            // 先根据ID把图片路径取出来
            $stmt = $this->_db->prepare("SELECT path FROM goods_image WHERE id IN({$_POST['del_image']})");
            $stmt->execute();
            $path = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // 循环每个图片的路径并删除
            foreach($path as $v)
            {
                @unlink(ROOT.'public/'.$v['path']);
            }

            // 从数据库中把图片的记录删除
            $stmt = $this->_db->prepare("DELETE FROM goods_image WHERE id IN({$_POST['del_image']})");
            $stmt->execute();
        }

        if($_FILES['image']['error'] == 0){

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
                    $goodsId,
                    $path
                ]);
            }
        }
    }

    public function getFullInfo($id){

        // 获取商品的基本信息
        $stmt = $this->_db->prepare("SELECT * FROM goods WHERE id=?");
        $stmt->execute([$id]);
        $info = $stmt->fetch(\PDO::FETCH_ASSOC);

        // 获取商品属性信息
        $stmt = $this->_db->prepare("SELECT * FROM goods_attribute WHERE goods_id=?");
        $stmt->execute([$id]);
        $attrs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 获取商品图片
        $stmt = $this->_db->prepare("SELECT * FROM goods_image WHERE goods_id=?");
        $stmt->execute([$id]);
        $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 获取商品SKU
        $stmt = $this->_db->prepare("SELECT * FROM goods_sku WHERE goods_id=?");
        $stmt->execute([$id]);
        $skus = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'info'=>$info,
            'images'=>$images,
            'skus'=>$skus,
            'attrs'=>$attrs
        ];
    }

}