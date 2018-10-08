drop table if exists privilege;
create table privilege
(
    id int unsigned not null auto_increment comment 'ID',
    pri_name varchar(255) not null comment '权限名称',
    url_path varchar(255) not null comment '对应的URL地址，多个地址用，隔开',
    parent_id int unsigned not null default '0' comment '上级权限的ID',
    primary key (id)
)engine=InnoDB comment='权限表';

drop table if exists role_privilege;
create table role_privilege
(
    pri_id int unsigned not null comment '权限ID',
    role_id int unsigned not null comment '角色ID',
    key pri_id(pri_id),
    key role_id(role_id)
)engine=InnoDB comment='角色权限表';

drop table if exists role;
create table role
(
    id int unsigned not null auto_increment comment 'ID',
    role_name varchar(255) not null comment '角色名称',
    primary key (id)
)engine=InnoDB comment='角色表';

drop table if exists admin_role;
create table admin_role
(
    role_id int unsigned not null comment '角色ID',
    admin_id int unsigned not null comment '管理员ID',
    key role_id(role_id),
    key admin_id(admin_id)
)engine=InnoDB comment='管理员表';

drop table if exists admin;
create table admin
(
    id int unsigned not null auto_increment comment 'ID',
    username varchar(255) not null comment '用户名',
    password varchar(255) not null comment '密码',
    primary key (id)
)engine=InnoDB comment='管理员表';


insert into privilege(id,pri_name,url_path,parent_id) VALUES
(1,'商品模块','',0),
    (2,'分类列表','category/index',1),
        (3,'添加分类','category/create,category/insert',2),
        (4,'修改分类','category/edit,category/update',2),
        (5,'删除分类','category/delete',2),
    (6,'品牌列表','brand/index',1),
        (7,'添加品牌','brand/create,brand/insert',6),
        (8,'修改品牌','brand/edit,brand/update',6),
        (9,'删除品牌','brand/delete',6);

insert into role_privilege(pri_id,role_id) VALUES
(6,2),
(7,2),
(8,2),
(9,2),
(1,3),
(2,3),
(3,3),
(4,3),
(5,3),
(6,3),
(7,3),
(8,3),
(9,3);

insert into role(id,role_name) VALUES
(1,'超级管理员'),
(2,'品牌编辑'),
(3,'商品总监');

insert into admin_role(role_id,admin_id) VALUES
(1,1),
(3,2),
(2,3);

insert into admin(id,username,password) VALUES
(1,'root','21232f297a57a5a743894a0e4a801fc3'),
(2,'tom','21232f297a57a5a743894a0e4a801fc3'),
(3,'jack','21232f297a57a5a743894a0e4a801fc3');