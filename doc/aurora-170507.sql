/**
 * 数据库 aurora
 * date 2017年5月7日 星期日
 * author Joshua Coenro
 * 数据表第二部分
**/
use `aurora`;

drop table if exists sys_visit;

/*==============================================================*/
/* Table: sys_visit                                             */
/*==============================================================*/
create table sys_visit
(
   listid               int not null auto_increment,
   ip                   varchar(15) not null,
   is_mobile            varchar(1) not null,
   agent                varchar(300),
   mtime                datetime not null default current_timestamp,
   dct                int not null,
   primary key (listid)
);



drop table if exists sys_key;

/*==============================================================*/
/* Table: sys_key (系统主键生成器)                               */
/*==============================================================*/
create table sys_key
(
   name                 varchar(80) not null,
   pref                 varchar(3),
   type                 varchar(2) not null default 'M',
   len                  int not null default 15,
   idx                  int not null default 1,
   mtime                datetime not null,
   primary key (name)
);
