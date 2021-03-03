
# PDOBridge

> Connect to a remote database via it's webserver and PHP without opening ports or allowing direct access to the DB server from outside sources.

PDOBridge attempts to wrap a PDO instance on a remote server. 

The client class in `/PDOBridgeClient` is to be included on the client server and
attempts to mimick all the methods of native PDO.

The `PDOBridgeHost` is meant to be hosted on the host server where the database
that the client needs access to lives. 

This is a quickly executed experiment. It may or may not have the potential to 
become a production quality tool, but it isn't currently, and probably shouldn't ever be 
anything more than a last resort. However, it is functional. 

See the `test.php` script for example usage.

### Example...

```php
$pdob = new PDOBc($dsn, $user, $pass, $pdob_host, $pdob_key);
$q = $pdob->prepare("select * from sample_table where age = ?");
$q->execute([138]);

while($res = $q->fetch(PDO::FETCH_ASSOC)){
	echo $res['name']." is ".$res['age']." years old<br>";
}
```