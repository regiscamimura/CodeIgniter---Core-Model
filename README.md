CodeIgniter - Core Model
========================

A core model library for CodeIgniter that performs the most used functions. 

Usage
=====

Drop the MY_Model.php file to application/core folder in a CodeIgniter instance. After that, your models should extend the MY_Model class instead of the CI_Model default class.

By doing that, your models will have 6 methods: get, listing, save, delete, count, and add_batch.

Get Method
==========

This method allows you to retrieve one record from a given database, and it takes 2 parameters. 

If the first parameter is a number, it will search by id. For example:

$this->user_model->get(1);

Will produce this query:

"SELECT * FROM user WHERE id = 1";

The first parameter can also be an array. For example:

$filter = array('name'=>'John', 'date >='=>'2014-10-10');
$this->user_model->get($filter);

Will product this query:

"SELECT * FROM user WHERE name = 'John' AND `date` >= '2014-10-10' LIMIT 1";

If you need a more complex filter, you can use a "raw" filter, like that:

$filter = array('date'=>'2014-10-10');
$filter["(name LIKE 'John%' OR name LIKE '%Connor'"] = false;
$this->user_model->get($filter);

This will product this query:

"SELECT * FROM user WHERE date = '2014-10-10' AND (name LIKE 'John%' OR name LIKE '%Connor')";

A second parameter can be used. If so, the query will retrieve only the field(s) you want. For example, if you want only one field, you can do that:

$name = $this->user_model->get(1, 'name');

That will return the value of the field "name" (if found). You can also retrieve more than one field:

$record = $this->user_model->get(1, 'name, status, date');

In that case, $record will be an array with the entries related to the fields "name", "status", and "date".

Listing Method
==============

This method is very similar to the get method, but instead of retrieving only one record, you'll want to retrieve a list of records. 

It also accepts two arguments, both optional. The first one must be an array, just like the $filter variable in the examples for the Get method. There are two "keywords" you can use here: "order_by" and "group_by". For example:

$filter = array("name"=>"John", "order_by"=>"name asc", "group_by"=>"id");
$this->user_model->listing($filter);

That will produce the following query:

"SELECT * FROM user WHERE name = 'John' ORDER BY name ASC GROUP BY id";

So order_by and group_by are keywords and won't be used as "where" arguments for the query.

You can pass a second argument. It must be a string in a specific format, for example:

$filter = array();
$listing = $this->user_model->listing($filter, "id=>name");

In this case, $listing will be an array which keys are the value of the "id" field, and the value of the entries will be the value of the field "name".

Save method
===========

This method is to insert OR update records in the database. It takes 2 parameters.

The first parameter it must be an array of values. For example:

$data = array('name'=>'John', 'date'=>'2014-10-10', 'status'=>'1');
$this->user_model->save($data);

The produced query will be:

"INSERT INTO user (name, date, status, created_by, created_at) VALUES ('John', '2014-10-10', '1', '$_SESSION['user_id']', date('Y-m-d H:i:s'))";

Note that the fields "created_by" and "created_at" are automatically added. That requires your database to have those fields. That's because this class was created to fit in a specific database structure. It's easy to modify the class to not use those fields, but for logging purposes, its generally a good idea to have those.

Also, note it will try to use a session variable. That's usual for admin areas where you have a logged user taking actions. The name of the session variable should be "user_id".

If the $data array has an "id" key, then an updated will be fired instead. For example:

$data = array('id'=>1, name'=>'John', 'date'=>'2014-10-10', 'status'=>'1');
$this->user_model->save($data);

The produced query will be:

"UPDATE user SET name = 'John', date = '2014-10-10', 'status' = '1', updated_by = '$_SESSION['user_id'], updated_at = 'date(Y-m-d H:i:s)' WHERE id = '1'";

If you want to update a record but using another field instead of id, you should use the second parameter. For example:

$data = array('id'=>1, name'=>'John', 'date'=>'2014-10-10', 'status'=>'1', 'account_id'=>2);
$this->user_model->save($data, 'account_id');

The produced query will be: 

"UPDATE user SET id = 1, name = 'John', date = '2014-10-10', 'status' = '1', updated_by = '$_SESSION['user_id'], updated_at = 'date(Y-m-d H:i:s)' WHERE account_id = 2";

Delete Method
=============

This method works just like the Get method, but it will take only one parameter. The parameter can be a number or an array. For example:

$this->user_model->delete(1);

Will produce:

"DELETE FROM user WHERE id = 1";

When the parameter is an array, for example:

$filter = array('name'=>'John', 'status'=>0);
$this->user_model->delete($filter);

The produced query will be:

"DELETE FROM user WHERE name = 'John' and status = '0'";

Additional Note
================

All the values are automatically escaped using CodeIgniter default mechanisms. 









