<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class crudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:crud {name} {--make=all} {--routes=true} {--breadcrumbs=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builder CRUD Laravel 8';
    protected $Field="";
    protected $primaryKey="";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getStub($type){
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    protected function generate_crud($make,$name){
        $results = DB::select("SHOW FIELDS FROM ".strtolower($name)."");
        $this->Field="[\n";
        foreach($results as $x=>$rs){
            if($rs->Key == "PRI"){
                $this->primaryKey = $rs->Field;
            }
            $this->Field .= "'".$rs->Field."',\n";
        }
        $this->Field = rtrim($this->Field,",\n")."\n]";
        if ($make == 'all') {
            $this->controller($name,$results);
        }elseif ($make == 'model') {
            $this->model($name);
        }elseif ($make == 'view') {
            $this->view($name,$results);
        }else{
            $this->controller($name,$results);
            $this->model($name);
            $this->view($name,$results);
        }
    }

    protected function controller($name,$table){
        
        $ModelValidation = "[\n";
        $ModelDefaultValue = "[\n";
        foreach($table as $x=>$rs){
            if($rs->Key == "PRI"){
                $this->primaryKey = $rs->Field;
            }

            if($rs->Extra != 'auto_increment'){
                $ModelValidation .= "'".$rs->Field."'   =>  ";
                $valid = "'',\n";
                if($rs->Null == 'NO'){
                    $valid = "'required',\n";
                }
                $ModelValidation .= $valid;
            }

            $ModelDefaultValue .= "'".$rs->Field."'   =>  '".($rs->Default??null)."',\n";
            $this->Field .= "'".$rs->Field."',\n";
        }
        $ModelValidation = rtrim($ModelValidation,",\n")."\n]";
        $ModelDefaultValue = rtrim($ModelDefaultValue,",\n")."\n]";
        
        $controllerTemplate = str_replace([
           '{{ModelName}}',
           '{{ModelGroup}}',
           '{{ModelRoute}}',
           '{{ModelField}}',
           '{{ModelPrimaryKey}}',
           '{{$ModelValidation}}',
           '{{$ModelDefaultValue}}',
        ],
        [
           $name,
           strtolower(Str::singular($name)),
           strtolower($name),
           $this->Field,
           $this->primaryKey,
           $ModelValidation,
           $ModelDefaultValue
        ],
        $this->getStub('Controller'));
        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
     }

     protected function model($name){
        $modelTemplate = str_replace(
           ['{{ModelName}}', '{{ModelTable}}', '{{ModelField}}','{{ModelPrimaryKey}}'],
           [$name, strtolower(Str::singular($name)),$this->Field,$this->primaryKey],
           $this->getStub('Model')
        );
        file_put_contents(app_path("Models/{$name}.php"), $modelTemplate);
     }
     
     protected function view($name,$table){
        $tableHeader = "";
        $name = strtolower($name);
        $field = Schema::getColumnListing($name);
        $tableHeader .= sprintf("'%s'", implode("','", $field ));
        $modelTemplate = str_replace(
           ['{{ModelCol}}', '{{ModelName}}'],
           [$tableHeader, strtolower(($name))],
           $this->getStub('v_index')
        );
        $patch = resource_path("views/{$name}");
		if (is_dir($patch)) {
			// delete_files($patch,TRUE);
			array_map('unlink', array_filter( 
				(array) array_merge(glob($patch."/*"))));
			rmdir($patch); 
		}
        mkdir($patch);
        file_put_contents($patch."/index.blade.php", $modelTemplate);
        //Create view form
        $form =[];
        foreach($table as $x=>$rs){
            $required = '';
            if($rs->Null == 'NO'){
               $required = '"required"  => "true"';
            }
            if($rs->Key != 'PRI') {
                $form[$x] = '{!! Create::input("'.$rs->Field.'",[
                    "value"     => $'.$name.'->'.$rs->Field.',
                    '.$required.'
                    ])->render("group"); 
                !!}';
            }
        }
        $form = implode("\n",$form);
        $modelTemplate = str_replace(
            ['{{ModelForm}}', '{{ModelName}}', '{{ModelPrimaryKey}}'],
            [$form, $name,$this->primaryKey],
            $this->getStub('v_form')
         );
        file_put_contents($patch."/form.blade.php", $modelTemplate);
     }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $make = $this->option('make');
        $routes = $this->option('routes');
        $breadcrumbs = $this->option('breadcrumbs');
        $this->info("
            You call create:user command \n
            with First Argument : {$name}
 
            with First Option : {$make} {$routes} {$breadcrumbs}
        ");

        $this->generate_crud($make,$name);
        if ($routes == 'true') {
            File::append(base_path('routes/web.php'),
            "
            Route::get('" . Str::singular(strtolower($name)) . "/get_dataTable','{$name}Controller@get_dataTable');
            Route::resource('" . Str::singular(strtolower($name)) . "', {$name}Controller::class);");
        }

        if ($breadcrumbs == 'true') {
            File::append(base_path('routes/breadcrumbs.php'),
            '
            Breadcrumbs::for("'. Str::singular(strtolower($name)) .'", function (BreadcrumbTrail $trail) {
                $trail->parent("home");
                $trail->push("'. Str::singular(strtolower($name)) .'", route("'. Str::singular(strtolower($name)) .'.index"));
            });');
        }
    }
}
