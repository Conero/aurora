/**
 * 数据库 aurora
 * date 2017年5月6日 星期六
 * author Joshua Coenro
 * powerdesigner 设置导出规则 默认： FK_%.U8:CHILD%_%.U9:REFR%_%.U8:PARENT%
 * 基础数据表，无数据 
 * 数据库名称: aurora
 * 数据库用户: emma
**/





--  创建数据库 'aurora'      -----------------------
drop database if exists `aurora`;
create database `aurora` default character set utf8 collate utf8_bin;
use `aurora`;

-- 用户  `emma`           -------------------------
-- drop user `emma` if exists;
create user 'emma'@'%' identified by '17conero0504';
grant all privileges ON `aurora`.* TO 'emma'@'%';

update mysql.user set password=password('17conero0504') where user='emma';


--  数据表格导出修改 powerdesigner 导出的脚本        -----------------------

drop table if exists atc1000c;

drop table if exists atc1002c;

drop table if exists file1000c;

drop table if exists msg1000c;

drop table if exists msg2000c;

drop table if exists sys_apis;

drop table if exists sys_const;

drop table if exists sys_file;

drop table if exists sys_group;

drop table if exists sys_login;

drop table if exists sys_recycle;

drop table if exists sys_role;

drop table if exists sys_user;

/*==============================================================*/
/* Table: atc1000c                                              */
/*==============================================================*/
create table atc1000c
(
   listid               int not null auto_increment,
   collected            varchar(100),
   title                varchar(100) not null,
   content              text not null,
   mtime                datetime not null default current_timestamp,
   uid                  int not null,
   primary key (listid)
);

/*==============================================================*/
/* Table: atc1002c                                              */
/*==============================================================*/
create table atc1002c
(
   listid               int not null auto_increment,
   pid                  int not null,
   comment              varchar(2000) not null,
   mtime                datetime not null default current_timestamp,
   uid                  int,
   primary key (listid)
);

/*==============================================================*/
/* Table: file1000c                                             */
/*==============================================================*/
create table file1000c
(
   listid               int not null auto_increment,
   descrip              varchar(150),
   remark               varchar(500),
   mtime                datetime not null default current_timestamp,
   uid                  int not null,
   primary key (listid)
);

/*==============================================================*/
/* Table: msg1000c                                              */
/*==============================================================*/
create table msg1000c
(
   listid               int not null auto_increment,
   sender_uid           int,
   content              varchar(1000) not null,
   recevie_uid          int not null,
   read_mk              varchar(1) not null,
   received_time        datetime not null default current_timestamp,
   read_time            datetime,
   primary key (listid)
);

/*==============================================================*/
/* Table: msg2000c                                              */
/*==============================================================*/
create table msg2000c
(
   listid               int not null auto_increment,
   send_uid             int,
   send_time            datetime not null default current_timestamp,
   content              varchar(1000) not null,
   receive_uid          int not null,
   read_mk              varchar(0) not null,
   primary key (listid)
);

/*==============================================================*/
/* Table: sys_apis                                              */
/*==============================================================*/
create table sys_apis
(
   listid               int not null auto_increment,
   url                  varchar(300) not null,
   password             varchar(100),
   param                varchar(100),
   count                int,
   descrip              varchar(500),
   mtime                datetime not null default current_timestamp,
   uid                  int not null,
   primary key (listid)
);

alter table sys_apis comment '1. password 单项加密以后的数据';

/*==============================================================*/
/* Table: sys_const                                             */
/*==============================================================*/
create table sys_const
(
   listid               int not null auto_increment,
   scope                varchar(30) not null,
   scope_desc           varchar(100),
   const_key            varchar(50),
   const_value          varchar(300),
   mtime                datetime not null default current_timestamp,
   uid                  int not null,
   primary key (listid)
);

/*==============================================================*/
/* Table: sys_file                                              */
/*==============================================================*/
create table sys_file
(
   listid               varchar(20) not null,
   name                 varchar(300) not null,
   path                 varchar(300) not null,
   fiiletype            varchar(50),
   pid                  int not null,
   mtime                datetime not null default current_timestamp,
   primary key (listid)
);

alter table sys_file comment '主键采用系统编码规则，filetype 对应 doctype mime，全局文件列表';

/*==============================================================*/
/* Table: sys_group                                             */
/*==============================================================*/
create table sys_group
(
   listid               int not null auto_increment,
   code                 varchar(50) not null,
   descrip              varchar(100) not null,
   remark               varchar(300),
   mtime                datetime not null default current_timestamp,
   uid                  int not null,
   primary key (listid)
);

/*==============================================================*/
/* Table: sys_login                                             */
/*==============================================================*/
create table sys_login
(
   listid               int not null auto_increment,
   uid                  int not null,
   mtime                datetime not null default current_timestamp,
   ip                   varchar(15),
   count                int not null,
   primary key (listid)
);

/*==============================================================*/
/* Table: sys_recycle                                           */
/*==============================================================*/
create table sys_recycle
(
   listid               int not null auto_increment,
   table_name           varchar(45) not null,
   col_data             varchar(1000) not null,
   mtime                datetime not null default current_timestamp,
   uid                  int,
   primary key (listid)
);

/*==============================================================*/
/* Table: sys_role                                              */
/*==============================================================*/
create table sys_role
(
   listid               int not null auto_increment,
   be_uid               int not null,
   code                 varchar(30) not null,
   descrip              varchar(80) not null,
   remark               varchar(300),
   pid                  int,
   mtime                datetime not null default current_timestamp,
   uid                  int not null,
   primary key (listid)
);

alter table sys_role comment 'code 角色可用来自于系常量';

/*==============================================================*/
/* Table: sys_user                                              */
/*==============================================================*/
create table sys_user
(
   uid                  int not null auto_increment,
   account              varchar(30) not null,
   certificate          varchar(100) not null,
   register_time        datetime not null default current_timestamp,
   last_time            datetime,
   last_ip              varchar(15),
   primary key (uid)
);

alter table atc1000c add constraint FK_Reference_15 foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table atc1002c add constraint FK_atc1002c__pid foreign key (pid)
      references atc1000c (listid) on delete restrict on update restrict;

alter table atc1002c add constraint FK_atc1002c__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table file1000c add constraint FK_file1000c__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table msg1000c add constraint FK_msg1000c__recevie_uid foreign key (recevie_uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table msg1000c add constraint FK_msg1000c__sender_uid foreign key (sender_uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table msg2000c add constraint FK_msg2000c__receive_uid foreign key (receive_uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table msg2000c add constraint FK_msg2000c__send_uid foreign key (send_uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table sys_apis add constraint FK_sys_apis__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table sys_const add constraint FK_sys_const__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table sys_file add constraint FK_sys_file__pid foreign key (pid)
      references file1000c (listid) on delete restrict on update restrict;

alter table sys_group add constraint FK_sys_group__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table sys_login add constraint FK_sys_login__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table sys_recycle add constraint FK_sys_recycle__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table sys_role add constraint FK_sys_role__be_uid foreign key (be_uid)
      references sys_user (uid) on delete restrict on update restrict;

alter table sys_role add constraint FK_sys_role__pid foreign key (pid)
      references sys_group (listid) on delete restrict on update restrict;

alter table sys_role add constraint FK_sys_role__uid foreign key (uid)
      references sys_user (uid) on delete restrict on update restrict;
