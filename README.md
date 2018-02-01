forgive me my poor english, god bless you can understand what i am saying

# composer
 composer require jeanku/database:dev-master

# initialization
you can set database config at entrance file(index.php) as follow:
    \Jeanku\Database\DatabaseManager::make(WEBPATH . '/config/database.php');

# extend model
you can extend the model as follow:
	class CoachModel extends \Jeanku\Database\Eloquent\Model

then you can use it as laravel model:
 $data = CoachModel::where('id', 10001)->get()->toArray();

# db
if you wantto use laravel DB to do sth, then you can use the class as this:
 use Jeanku\Database\DatabaseManager as DB;
then you can use it as laravel DB:
 $data = DB::table('table')->where('id', 10001)->get();
 
	


 
