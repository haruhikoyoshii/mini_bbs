create database mini_bbs;
use mini_bbs;

create table members(
  id int primary key auto_increment,
  name varchar(255) not null,
  email varchar(255) not null,
  password varchar(100) not null,
  picture  varchar(255) not null,
  created datetime not null,
  modified timestamp not null
);
create table posts(
  id int primary key auto_increment,
  message text not null,
  member_id int not null,
  reply_message_id int not null,
  created datetime not null,
  modified timestamp not null
);
