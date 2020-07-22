<?php

namespace dimonka2\flatstate\Commands;

use dimonka2\flatstate\FlatstateService;
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

        $this->stateClass = FlatstateService::stateClass();
        $this->info('State class: ' . self::format($this->stateClass, 'model'));

        if($this->argument('category')) {
            $states = FlatstateService::getStateList($this->argument('category'));
            $fields = array_merge([
                'state_type', 'state_key', 'name', 'id'
            ], FlatstateService::getStateFillable());
            $states = collect($states)->map(function ($item, $key) use($fields) {
                $out = [];
                foreach($fields as $field) {
                    $out[] = $item->{$field};
                }
                return $out;
            });
            $this->table($fields, $states);
        } else {
            $categories = $this->stateClass::select('state_type', DB::raw('count(*)'))->groupBy('state_type')->get();
            $this->table(['state_type', 'count' ], $categories);
        }

    }
}
