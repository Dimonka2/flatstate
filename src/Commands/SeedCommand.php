<?php

namespace dimonka2\flatform\Commands;

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

    protected function processModelStates($modelClass)
    {
        $this->info('Processing model: ' . self::format($modelClass, 'model'));
        $usingTrait = in_array(
            Stateable::class, 
            array_keys((new \ReflectionClass($modelClass))->getTraits())
        );
        if(!$usingTrait) {

        }
        $modelStates = (new $modelClass)->getStates();
        foreach ($modelStates as $state => $definition) {
            $state_type = $definition['state_type'] ?? null;
            if(!is_string($state_type)) {

            }            

            $state = $this->manager->selectState($key);
            if($state == null) {
                $state = new State;
                $state->state_key = $key;
            }
            $state->name = $name;
            $state->state_type = $type;
            $state->icon = $icon;
            $state->color = $color;
            if (!$state->id) {
                $state->save();
            } else {
                $state->update();
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
        $this->info(self::format('Seeding states', 'title'));
        $this->addStyle('model', 'green', 'default', ['bold']);
        $this->stateClass = Flatstate::stateClass();
        $this->info('State class: ' . self::format('Seeding states', 'model'));
        $models = Flatstate::config('models');
        $this->manager = app('flatstates');
        $this->manager->clearCache();
        
        foreach ($models as $modelClass) {
            $this->processModelStates($modelClass);
        }
    }
}
