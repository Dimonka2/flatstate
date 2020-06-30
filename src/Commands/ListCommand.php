<?php

namespace dimonka2\flatstate\Commands;

use dimonka2\flatstate\Flatstate;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;


class ListCommand extends Command
{
    use StyledTrait;
    protected $stateClass;

       /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flatstate:list {category?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List availabe categories or states in category. ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->addStyle('title', 'red', 'default', ['bold']);
        $this->addStyle('model', 'green', 'default', ['bold']);
        $this->info(self::format('Listing states', 'title'));

        $this->stateClass = Flatstate::stateClass();
        $this->info('State class: ' . self::format($this->stateClass, 'model'));

        if($this->argument('category')) {
            $states = Flatstate::getStateList($this->argument('category'));
            $states = collect($states)->map(function ($item, $key) {
                return [
                    $item->state_type, $item->state_key, $item->name, $item->id,
                ];
            });
            $this->table(['state_type', 'state_key', 'name', 'id' ], $states);
        } else {
            $categories = $this->stateClass::select('state_type', DB::raw('count(*)'))->groupBy('state_type')->get();
            $this->table(['state_type', 'count' ], $categories);
        }

    }
}
