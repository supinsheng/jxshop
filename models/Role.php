<?php
namespace models;

class Role extends Model
{
    // 设置这个模型对应的表
    protected $table = 'role';
    // 设置允许接收的字段
    protected $fillable = ['role_name'];

    // 添加、修改角色之后自动被执行
    // 获取添加完之后的ID； $this->data['id']
    protected function _after_write()
    {
        $roleId = isset($_GET['id']) ? $_GET['id'] : $this->data['id'];

        // 删除原权限
        $stmt = $this->_db->prepare('DELETE FROM role_privilege WHERE role_id=?');
        $stmt->execute([$roleId]);

        $stmt = $this->_db->prepare("INSERT INTO role_privilege(pri_id,role_id) VALUES(?,?)");
        // 循环所有勾选的权限ID插入到中间表
        foreach($_POST['pri_id'] as $v)
        {
            $stmt->execute([
                $v,
                $roleId
            ]);
        }
    }

    // 在删除之前执行
    protected function _before_delete()
    {
        $stmt = $this->_db->prepare("delete from role_privilege where role_id=?");
        $stmt->execute([
            $_GET['id']
        ]);
    }

    public function getPriIds($roleId){

        // 预处理
        $stmt = $this->_db->prepare('SELECT pri_id FROM role_privilege WHERE role_id=?');
        // 执行
        $stmt->execute([$roleId]);
        // 取数据
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 转成一维数组
        $_ret = [];
        foreach($data as $k=>$v){
            $_ret[] = $v['pri_id'];
        }

        return $_ret;
    }
}