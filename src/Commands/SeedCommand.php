<?php

namespace dimonka2\flatstate\Commands;

use Illuminate\Console\Command;
use dimonka2\flatstate\Flatstate;
use Illuminate\Database\Eloquent\Model;
use dimonka2\flatstate\Traits\Stateable;
use Symfony\Component\ClassLoader\ClassMapGenerator;

class SeedCommand extends Command
{
    use StyledTrait;

    protected $usedStates = [];
    protected $stateClass;
    private $processedModels = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flatstate:seed {class?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed states of the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function processState($state_type, array $definition)
    {
        $key = $definition['key'] ?? null;
        $name = $definition['name'] ?? null;
        if(!$key) {
            $this->info(self::format('State record has no "key"', 'error'));
            return;
        }
        if(!$name) {
            $this->info(self::format('State record has no "name"', 'error'));
            return;
        }
        $state = $this->manager->selectState($key);

        if($state == null) {
            $state = new $this->stateClass;
            $state->state_key = $key;
            $state->state_type = $state_type;
        }
        $state->name = $name;
        $state->fill($definition);
        if (!$state->id) {
            $state->save();
        } else {
            $state->update();
        }

        if($this->verbose){
            $properties = '';
            if($definition['icon'] ?? false) $properties .= 'icon: ' . $definition['icon'] . '; ';
            if($definition['color'] ?? false) $properties .= 'color: ' . $definition['color'] . '; ';
            $this->info("\t\tseeding: ". self::format($key, 'model') . ' / ' . $name . ($properties ? ' - ' . $properties : ""));
        }
    }

    protected function processModelStates(array $modelStates)
    {
        foreach ($modelStates as $state => $definition) {
            $state_type = $definition['type'] ?? null;

            if(!is_string($state_type)) {
                $this->info(self::format('Following state does not have type', 'error') . ': ' . $state);
                return;
            }

            $this->info("\tseeding states: " . self::format($state, 'model') . ' type: ' . self::format($state_type, 'model') );
            foreach($definition as $state){
                if(is_array($state)) {
                    $this->processState($state_type, $state);
                }
            }
        }
    }

    protected function processModelClass($modelClass, $verified = false)
    {
        if(isset($this->processedModels[$modelClass])) return;
        // mark this model as processed
        $this->processedModels[$modelClass] = 1;

        $this->info("\tprocessing model: " . self::format($modelClass, 'model'));
        if(!$verified){
            $usingTrait = in_array(
                Stateable::class,
                array_keys((new \ReflectionClass($modelClass))->getTraits())
            );
            if(!$usingTrait) {
                $this->info(self::format('This is not a statable model', 'error'), ': ' . self::format($modelClass, 'model'));
                return;
            }
        }
        $modelStates = (new $modelClass)->getStates();

        $this->processModelStates($modelStates);
    }

    protected function processFolder($folder)
    {

        $this->info("\nProcessing folder: " . self::format($folder, 'folder'));

        $models = collect(ClassMapGenerator::createMap(base_path($folder)));

        foreach ($models as $class => $path) {

            $reflection = new \ReflectionClass($class);
            $valid = $reflection->isSubclassOf(Model::class) &&
                        !$reflection->isAbstract() && in_array(
                            Stateable::class,
                            array_keys($reflection->getTraits())
                        );

            if($valid) {
                $this->processModelClass($class, true);
            } else {
                $this->info("\tclass " . self::format($class, 'model') . self::format(' is not stateable.', 'title'));
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->verbose = $this->hasOption('verbose');
        $this->addStyle('title', 'bright-red', 'default', ['bold']);
        $this->addStyle('error', 'white', 'red', ['bold']);
        $this->info(self::format('Seeding DB states.', 'title'));
        $this->addStyle('model', 'yellow', 'black', ['bold']);
        $this->addStyle('folder', 'bright-green', 'black', ['bold']);
        $this->stateClass = Flatstate::stateClass();
        $this->info('State class: ' . self::format($this->stateClass, 'model'));

        $this->manager = app('flatstates');
        $this->manager->clearCache();

        // process single models
        $models = Flatstate::config('models', []);
        if(is_array($models)){
            foreach ($models as $modelClass) {
                $this->processModelClass($modelClass);
            }
        }

        // process model folders
        $folders = Flatstate::config('folders', ['app/Models']);
        if(is_array($folders)){
            foreach ($folders as $folder) {
                $this->processFolder($folder);
            }
        }

        $this->manager->clearCache();
    }
}
