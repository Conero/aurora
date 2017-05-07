-- 2017年5月7日 星期日
-- 获取主键值

drop function if exists `get_skey`; -- 同同时执行报错
delimiter //
create function `get_skey`(
    v_name varchar(50)
)
returns varchar(30)
begin
    declare retval varchar(30) default '';
    
    declare v_pref varchar(3) default '';
    declare v_type varchar(2) default 'M';
    declare v_len int default 15;
    declare v_idx int  default 1;

    declare v_has int;
    
    -- 默认测试值
    if v_has is null then
        set v_name = 'sys_test_key';
    end if;

    select ifnull(count(*),0) into v_has from `sys_key` where `name`=v_name;
    if v_has > 0 then 
        select `name`,`pref`,`type`,`len`,`idx` into v_name,v_pref,v_type,v_len,v_idx from `sys_key` where `name`=v_name;
        set v_idx = v_idx + 1;
        -- 类型判断
        set retval = concat(v_pref,date_format(now(),'%Y%d'));
        set retval = concat(retval,lpad(v_idx,(v_len - length(retval)),'0'));
        update `sys_key` set `idx`=v_idx,`mtime`=current_timestamp() where `name`=v_name;
    elseif v_has is not null then 
        set retval = concat(v_pref,date_format(now(),'%Y%d'));
        set retval = concat(retval,lpad(v_idx,(v_len - length(retval)),'0'));
        insert into `sys_key` (`name`,`pref`,`type`,`len`,`idx`) values (v_name,v_pref,v_type,v_len,v_idx);
    end if;

    return retval;
end;