<?php


namespace App\Console\Commands;


use App\Models\User;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = "test:go";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "test commands";


    public function handle()
    {

        $id = 1;
        $user = User::where('id',$id)->first();
        $user->setLogin('ogo go login');
        $user->save();
        $login =  $user->getLogin();

        $id =100500;
        $user = User::findOrFail($id);
        $this->info('test command ok');
    }
}
