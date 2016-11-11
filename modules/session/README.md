### Session MiddleWare.
* php default $_SESSION implements.
* get the current session_name in cookie.
```javascript
$req->session->getName();
```
* get the current session_id
```javascript
$req->session->getID();
```
* set session value
```javascript
$req->session->username = 'hello';
```
*get the session value
```javascript
echo $req->session->username;
```
* delete session.
```javascript
$req->session->delete('key');
```
* clear all session.
```javascript
* $qpp->$req->session->clear();
```
* if you want to change the session_name和session_timeout, please change the core options['session_name','session_timeout'] in the package.json config file.