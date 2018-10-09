<?php
namespace models;

class Admin extends Model
{
    // 设置这个模型对应的表
    protected $table = 'admin';
    // 设置允许接收的字段
    protected $fillable = ['username','password'];

    public function _before_write(){
        $this->data['password'] = md5($this->data['password']);
    }

    public function _after_write(){

        $id = isset($_GET['id']) ? $_GET['id'] : $this->data['id'];

        // 删除原数据
        $stmt = $this->_db->prepare('DELETE FROM admin_role WHERE admin_id=?');
        $stmt->execute([$id]);

        // 重新添加新勾选的数据
        $stmt = $this->_db->prepare('INSERT INTO admin_role(admin_id,role_id) VALUES(?,?)');

        foreach($_POST['role_id'] as $v){

            $stmt->execute([
                $id,
                $v
            ]);
        }
    }

    public function login($username,$password){

        $stmt = $this->_db->prepare('SELECT * FROM admin WHERE username=? AND password=?');
        $stmt->execute([
            $username,
            md5($password)
        ]);

        $info = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($info){
            $_SESSION['id'] = $info['id'];
            $_SESSION['username'] = $info['username'];

            // 查看该管理员是否有一个角色ID=1
            $stmt = $this->_db->prepare('SELECT COUNT(*) FROM admin_role WHERE role_id=1 AND admin_id=?');
            $stmt->execute([$_SESSION['id']]);
            $c = $stmt->fetch(\PDO::FETCH_COLUMN);

            if($c>0){
                $_SESSION['root'] = true;
            }else {
                // 取出这个管理员有权访问的路径
                $_SESSION['url_path'] = $this->getUrlPath($_SESSION['id']);
            }

        }else {
            throw new \Exception('用户名或者密码错误！');
        }
    }

    public function logout(){
        $_SESSION = [];
    }

    public function getUrlPath($adminId){
        $sql = "SELECT c.url_path FROM admin_role a
                 LEFT JOIN role_privilege b ON a.role_id=b.role_id
                 LEFT JOIN privilege c ON b.pri_id=c.id
                 WHERE a.admin_id=? AND c.url_path!=''";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute([$adminId]);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $_ret = [];
        foreach($data as $v){
            if(FALSE === strpos($v['url_path'],',')){
                $_ret[] = $v['url_path'];
            }else {
                $_tt = explode(',',$v['url_path']);
                $_ret = array_merge($_ret,$_tt);
            }
        }
       
        return $_ret;
    }
}