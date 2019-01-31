drop database if exists aboutLukas;

create database aboutLukas;

use aboutLukas;

create table project
  (
    id int not null primary key auto_increment,
    title varchar(255) not null,
    branch varchar(50) not null,
    link varchar(255) not null,
    description text not null
  );

create table user (
  id int not null primary key auto_increment,
  email varchar(100) not null,
  password varchar(255) not null
);

insert into project (id, title, branch, link, description) values
(null, 'BroomCall', 'Cleaning service', 'http://lukas713.byethost17.com/bootstrapBroomCall/', 'BroomCall is a web application that solves many problems to company that
has cleaning services as main priority. It basicaly web page with all the informations
that can serve to you as a customer. BroomCall is imaginary company. As my first project,
presentation and application part are all combined but I earned so mouch. I learned a little bit
of jQuery and AJAX too. But mostly procedural PHP and MySql.'
);