# PHP-MVC Demo

A simple PHP-based MVC framework. Has the following features:
* MVC structure.
* Route class to handle urls and send requests to correct controllers and actions.
* Request class that handles POST and GET data.
* Session data manager.
* Authorization class using simple permission-based authorizations.
* Model makes database requests via PDO and supports basic CRUD operations.
* Model does content validation through customizable rulesets.
* Model supports before- and after-execution calls.
* View has HTML helper classes to create basic HTML, Model-based forms and tables, and page navigators.
* User registration and login.
* Migrations system for updating the database.

Uses [Bootstrap](https://getbootstrap.com/) (V5) for prettier layout and [Summernote](https://summernote.org/)-editor as article content editor.

## USAGE

Requires a database connection (tested on Apache & MySQL). Project root contains a db.sql file that can execute database creation. The database contains an admin-level default user (username admin, password admin) that has higher administrative permissions than users created through registration page.

The config.php.example under config directory must be renamed to config.php. Database connection details should be written to its defines and the URLROOT define should be set to point to the project's location (default is 'localhost/mvc_demo').

## TODO

* Rewrite auth system to use role-based permissions, so for example content update actions can be restricted to content owner (right now everything goes through if form-data is directly tampered with).
* Improve error display and management.
* An image gallery page and manager to demonstrate file uploading.