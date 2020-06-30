<?php

namespace dimonka2\flatstate\Commands;

use dimonka2\flatstate\Flatstate;
use dimonka2\flatstate\Traits\Stateable;
use Illuminate\Console\Command;


class SeedCommand extends Command
{
    use StyledTrait;

    protected $usedStates = [];
    protected $stateClass;
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
    }

    protected function processModelStates($modelClass)
    {
        $this->info('Processing model: ' . self::format($modelClass, 'model'));
        $usingTrait = in_array(
            Stateable::class,
            array_keys((new \ReflectionClass($modelClass))->getTraits())
        );
        if(!$usingTrait) {
            $this->info(self::format('This is not a statable model', 'error'), ': ' . self::format($modelClass, 'model'));
            return;
        }
        $modelStates = (new $modelClass)->getStates();
        foreach ($modelStates as $state => $definition) {
            $state_type = $definition['type'] ?? null;

            if(!is_string($state_type)) {
                $this->info(self::format('Following state does not have type', 'error') . ': ' . $state);
                return;
            }

            $this->info('Seeding states: ' . self::format($state, 'model') . ' type: ' . self::format($state_type, 'model') );
            foreach($definition as $state){
                if(is_array($state)) {
                    $this->processState($state_type, $state);
                }
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
        $this->addStyle('title', 'red', 'default', ['bold']);
        $this->addStyle('error', 'white', 'red', ['bold']);
        $this->info(self::format('Seeding states', 'title'));
        $this->addStyle('model', 'green', 'default', ['bold']);
        $this->stateClass = Flatstate::stateClass();
        $this->info('State class: ' . self::format($this->stateClass, 'model'));
        $models = Flatstate::config('models');
        $this->manager = app('flatstates');
        $this->manager->clearCache();

        foreach ($models as $modelClass) {
            $this->processModelStates($modelClass);
        }

        $this->manager->clearCache();
    }
}
