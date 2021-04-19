<?php

namespace App\Console\Commands;

use App\Http\Controllers\QueensController;
use Illuminate\Console\Command;

class Queens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queens:solve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solves Queens Puzzle';

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
     * @return int
     */
    public function handle()
    {
        $this->line(
            sprintf('Placing %d queens on an (%d,%d) sized board that cannot attack each other.',
            QueensController::QUEEN_COUNT + 1,
            QueensController::MAX_X + 1,
            QueensController::MAX_Y +1,
        ));

        $controller = new QueensController();
        $result = $controller->solve();

        $this->line("Result after {$result['tries']} tries:");
        $this->drawMap($controller->queens);
        $this->newLine();
        $this->line("Solution fingerprint: {$result['fingerPrint']}");

        return true;
    }

    /**
     * Draw ASCII map of queens positions.
     *
     * @param $queens
     */
    protected function drawMap($queens)
    {
        $horizontalSeparator = ' |' . str_repeat('-', ((QueensController::MAX_X + 1) * 4) - 1) . '|';

        $mapLine = '';
        for ($y = QueensController::MAX_Y; $y >= QueensController::MIN_Y; $y--) {
            $this->line($horizontalSeparator);
            for ($x = QueensController::MIN_X; $x <= QueensController::MAX_X; $x++) {
                $add = "| . ";
                foreach ($queens as $queen) {
                    if ($queen->x === $x && $queen->y === $y) {
                        $add = '| Q ';
                    }
                }
                $mapLine .= $add;
            }

            $this->line($y . $mapLine . '|');
            $mapLine = '';
        }
        $this->line($horizontalSeparator);

        $xGridNumbers = ' ';
        for ($x = 0; $x <= QueensController::MAX_X; $x++) {
            $xGridNumbers .= "  $x ";
        }

        $this->line($xGridNumbers);
    }
}
