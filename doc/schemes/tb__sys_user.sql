-- 数据表 sys_user 修改操作，执行数据表导入脚本以后忽略脚本

alter table `sys_user` add (
    `name`                 varchar(100) not null,
    `gender`               varchar(1)
);