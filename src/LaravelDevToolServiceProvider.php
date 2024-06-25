<?php

namespace Akkey247\LaravelDevTool;

use Illuminate\Support\ServiceProvider;
use Akkey247\LaravelDevTool\Console\Commands\MakeModelCrudCommand;
use Akkey247\LaravelDevTool\Console\Commands\RemoveModelCrudCommand;

class LaravelDevToolServiceProvider extends ServiceProvider
{
    /**
     * サービスコンテナへの登録
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * サービスの初期処理
     *
     * @return void
     */
    public function boot()
    {
        $this->addCommands();
    }

    /**
     * コマンドを追加
     *
     * @return void
     */
    private function addCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModelCrudCommand::class,
                RemoveModelCrudCommand::class,
            ]);
        }
    }
}
