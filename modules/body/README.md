# This module is used to implement the Application->Request->body.
* $app->$req->body->content();//format php post, php://input
* $app->$req->body->files_num();//http upload file numbers, such as <form><input type='file'/>
* $app->$req->body->key;//get the post value key.