/**
 * 系统默认数据
 * date 2017年5月7日 星期日
 * author Joshua Coenro
 * 新增-系统用户
**/

-- 新增系统用户 a_sys
delete from `sys_user` where `account`='a_sys';
insert into `sys_user` (`uid`,`account`,`certificate`,`name`) values 
    (1,'a_sys','init-invalite','系统')
;

-- 用户分组以及角色
delete from `sys_role` where `listid`=1;
delete from `sys_group` where `listid`=1;

insert into `sys_group` (`listid`,`code`,`descrip`,`uid`) values
    (1,'sys','网站',1)
;
insert into `sys_role` (`listid`,`be_uid`,`code`,`descrip`,`pid`,`uid`) values
    (1,1,'developer','开发者',1,1)
;