<?php

namespace Akkey247\LaravelDevTool\Console\Commands;

use Illuminate\Console\Command;

class PublishComponentsCommand extends Command
{
    /**
     * コマンド名と引数、オプションの定義
     *
     * @var string
     */
    protected $signature = 'publish:components {targets*}';

    /**
     * コマンドの説明
     *
     * @var string
     */
    protected $description = 'コンポーネントを公開';

    /**
     * コマンド処理
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Publishing components...');
    }
}
