<?php

namespace dimonka2\flatstate\Commands;

use dimonka2\flatstate\FlatstateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;


class GenTSCommand extends Command
{
    use StyledTrait;
    protected $stateClass;

       /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flatstate:generate {category?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Typescript list of all states or states in category. ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function exportCategory(string $category) {
        $states = FlatstateService::getStateList($category);
        $fields = array_merge([
            'state_key', 'name', 'color', 'icon'
        ], FlatstateService::getStateFillable());
        $states = collect($states)->map(function ($item, $key) use($fields) {
            $out = [];
            foreach($fields as $field) {
                $out[$field] = $item->{$field};
            }
            return $out;
        });
        $this->info("\texport const " . Str::studly($category) . 'States = '  . "\t{");
        $states->each(function ($item) {
            $color = $item['color'] ?? null;
            $content = "\t{ label:\t'" . $item['name']  . "',\tcolor:\t" .
                ( $color ? "'$color'" : 'undefined')  . " },";
            $this->info("\t\t" . self::format($item['state_key'], 'model') . ': ' . $content);
        });
        $this->info("\t} as const");

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->addStyle('title', 'red', 'default', ['bold']);
        $this->addStyle('model', 'yellow', 'default', ['bold']);
        $this->info(self::format('Listing state definitions', 'title'));

        if($this->argument('category')) {
            $category = $this->argument('category');
            $this->exportCategory($category);
        } else {
            $categories = $this->stateClass::select('state_type', DB::raw('count(*)'))->groupBy('state_type')->get();
            $this->table(['state_type', 'count' ], $categories);
        }

    }
}
