namespace controllers;

class <?=$cname?> {

    // 列表页
    public function index(){
        view('<?=$tableName?>.index');
    }

    // 显示添加的表单
    public function create(){
        view('<?=$tableName?>.create');
    }

    // 处理添加表单
    public function insert(){

    }

    // 显示修改的表单
    public function edit(){
        view('<?=$tableName?>.edit');
    }

    // 修改表单的方法
    public function update(){

    }

    // 删除
    public function delete(){

    }
}